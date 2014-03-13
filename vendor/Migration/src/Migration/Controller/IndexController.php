<?php
namespace Migration\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Console\Request as ConsoleRequest;

class IndexController extends AbstractActionController
{
    const REVISIONS_PATH = '/data/revisions/';
    
    const CLASS_NAME = 'Db_Revision_';
    
    protected $_tableName = 'version';
    
    protected $_revisionsList = array();
    
    protected $_appliedRevisionsList = array();
    
    protected $_rootPath = '';
    
    protected $_db;
    
    public function __construct()
    {
        $this->_rootPath = getcwd();
        $this->_initRevisionsList();
    }
    
    public function upAction()
    {
        $this->_db = $this->getServiceLocator()->get('db');
        $this->_initAppliedRevisionsList();
        
        $current = $this->_getLatestRevision();
        
        echo "Current revision: {$current}\n";
        
        $params = $this->getRequest()->getContent();
        if (isset($params[2]) && is_numeric($params[2])) {
            $toRevision = (int) $params[2];
            if ($toRevision <= $current) {
                return "Current revision is greater or equal than specified. If you want to rollback, use 'down' action\n";
            } else {
                foreach ($this->_revisionsList as $revision) {
                    if ($revision > $toRevision) {
                        break;
                    }
                    if (!$this->_isRevisionApplied($revision)) {
                        $this->_upRevision($revision);
                        echo "Applying revision {$revision} has been completed\n";
                    }
                }
            }
        } else {
            foreach ($this->_revisionsList as $revision) {
                if (!$this->_isRevisionApplied($revision)) {
                    $this->_upRevision($revision);
                    echo "Applying revision {$revision} has been completed\n";
                }
            }
        }
    }
    
    public function downAction()
    {
        $this->_db = $this->getServiceLocator()->get('db');
        $this->_initAppliedRevisionsList();
        $current = $this->_getLatestRevision();
        echo "Current revision: {$current}\n";
        $params = $this->getRequest()->getContent();
        if (isset($params[2]) && is_numeric($params[2])) {
            $toRevision = (int) $params[2];
            if ($toRevision >= $current) {
                return "Current revision is smaller or equal than specified. If you want to update, use 'up' action\n";
            } else {
                $list = $this->_revisionsList;
                rsort($list);
                foreach ($list as $revision) {
                    if ($revision > $toRevision) {
                        $this->_downRevision($revision);
                        echo "Rolling back to revision {$revision} has been completed\n";
                    } else {
                        break;
                    }
                }
            }
        } else {
            echo "You must specify to which revision should we rollback\n";
        }
    }
    
    public function createAction()
    {
        $number = $this->_getNewRevisionNumber();
        
        $newRevision = \Zend\Code\Generator\FileGenerator::fromArray(
            array(
                'class' => array(
                        'name' => self::CLASS_NAME . $number,
                        'properties' => array(
                            \Zend\Code\Generator\PropertyGenerator::fromArray(array(
                                'name'         => 'upQuery',
                                'visibility'   => 'public',
                                'defaultValue' => new \Zend\Code\Generator\PropertyValueGenerator('<<<EOD' . "\n\n\n\n" . 'EOD', 'int')
                            )),
                            \Zend\Code\Generator\PropertyGenerator::fromArray(array(
                                'name'         => 'downQuery',
                                'visibility'   => 'public',
                                'defaultValue' => new \Zend\Code\Generator\PropertyValueGenerator('<<<EOD' . "\n\n\n\n" . 'EOD', 'int')
                            ))
                        ),
                        'methods' => array(
                            \Zend\Code\Generator\MethodGenerator::fromArray(array(
                                'name'       => 'preUp',
                                'visibility' => 'public',
                                'parameters' => array(
                                    \Zend\Code\Generator\ParameterGenerator::fromArray(array(
                                        'name' => 'db',
                                        'type' => '\Zend\Db\Adapter\Adapter'
                                    ))
                                )
                            )),
                            \Zend\Code\Generator\MethodGenerator::fromArray(array(
                                'name'       => 'postUp',
                                'visibility' => 'public',
                                'parameters' => array(
                                    \Zend\Code\Generator\ParameterGenerator::fromArray(array(
                                        'name' => 'db',
                                        'type' => '\Zend\Db\Adapter\Adapter'
                                    ))
                                )
                            )),
                            \Zend\Code\Generator\MethodGenerator::fromArray(array(
                                'name'       => 'preDown',
                                'visibility' => 'public',
                                'parameters' => array(
                                    \Zend\Code\Generator\ParameterGenerator::fromArray(array(
                                        'name' => 'db',
                                        'type' => '\Zend\Db\Adapter\Adapter'
                                    ))
                                )
                            )),
                            \Zend\Code\Generator\MethodGenerator::fromArray(array(
                                'name'       => 'postDown',
                                'visibility' => 'public',
                                'parameters' => array(
                                    \Zend\Code\Generator\ParameterGenerator::fromArray(array(
                                        'name' => 'db',
                                        'type' => '\Zend\Db\Adapter\Adapter'
                                    ))
                                )
                            ))
                        )
                )
            )
        );
        
        $revision = $newRevision->generate();
        file_put_contents($this->_rootPath . self::REVISIONS_PATH . $number . '.php', $revision);
        if (PHP_OS == 'Linux') {
            chmod($this->_rootPath . self::REVISIONS_PATH . $number . '.php', 0666);
        }
        
        echo "New revision {$number}.php has been created\n";
    }
    
    public function setupAction()
    {
        $this->_init();
        
        $sql = <<<EOD
        
        CREATE TABLE IF NOT EXISTS `{$this->_tableName}` (
            `version_number` INT(10) UNSIGNED NOT NULL DEFAULT '0'
        ) ENGINE = MYISAM;
        
EOD;
        
        $config = $this->getServiceLocator()->get('Config');
        
        $file = tempnam(sys_get_temp_dir(), 'Setup');
        file_put_contents($file, $sql);
        
        $cmd = "mysql -u {$config['db']['username']} --password={$config['db']['password']} -B {$config['db']['dbname']} < {$file}";
        exec($cmd, $output);
        unlink($file);
        
        $output = implode("\n", $output);
        if (!empty($output)) {
            return $output;
        }
        echo "Setup completed\n";
        
        $this->upAction();
    }
    
    protected function _initRevisionsList()
    {
        $revisions = array();
        $iterator = new \DirectoryIterator($this->_rootPath . self::REVISIONS_PATH);
        foreach ($iterator as $item) {
            if ($item->isFile()) {
                $name = str_replace('.php', '', $item->getFilename());
                if (is_numeric($name)) {
                    $revisions[] = (int) $name;
                }
            }
        }
        sort($revisions);
        $this->_revisionsList = $revisions;
    }
    
    protected function _initAppliedRevisionsList()
    {
        $revisions = array();
        $rows = $this->_db->query("SELECT `version_number` FROM `{$this->_tableName}` ORDER BY `version_number` ASC")->execute();
        foreach ($rows as $row) {
            $revisions[] = $row['version_number'];
        }
        $this->_appliedRevisionsList = $revisions;
    }
    
    protected function _getNewRevisionNumber()
    {
        return time();
    }
    
    protected function _isRevisionApplied($revision)
    {
        return in_array($revision, $this->_appliedRevisionsList);
    }
    
    protected function _getLatestRevision()
    {
        return max(!empty($this->_appliedRevisionsList) ? $this->_appliedRevisionsList : array(0));
    }
    
    protected function _saveRevision($revision)
    {
        $this->_db->query("INSERT INTO `{$this->_tableName}` VALUES ({$revision})")->execute();
    }
    
    protected function _removeRevision($revision)
    {
        $this->_db->query("DELETE FROM `{$this->_tableName}` WHERE `version_number` = {$revision}")->execute();
    }
    
    protected function _revisionExists($number)
    {
        return in_array($number, $this->_revisionsList);
    }
    
    protected function _init()
    {
        $config = $this->getServiceLocator()->get('Config');
        $params = $this->getRequest()->getContent();
    
        $username = (isset($params[2])) ? $params[2] : 'root';
        $password = (isset($params[3])) ? $params[3] : '';
    
        $sql = <<<EOD
    
        DROP DATABASE IF EXISTS {$config['db']['dbname']};
        CREATE DATABASE IF NOT EXISTS {$config['db']['dbname']};
        GRANT ALL ON {$config['db']['dbname']}.* TO '{$config['db']['username']}'@'{$config['db']['host']}'
        IDENTIFIED BY '{$config['db']['password']}' WITH GRANT OPTION;
    
EOD;
    
        $file = tempnam(sys_get_temp_dir(), 'Init');
        file_put_contents($file, $sql);
        
        $cmd = "mysql -u {$username} --password={$password} < {$file}";
        exec($cmd, $output);
        
        unlink($file);
        
        $output = implode("\n", $output);
        if (!empty($output)) {
            return $output;
        }
        echo "Init completed\n";
    }
    
    protected function _upRevision($number)
    {
        $loadFile = $this->_rootPath . self::REVISIONS_PATH . $number . '.php';
        $className = self::CLASS_NAME . $number;
        if (is_readable($loadFile)) {
            include_once $loadFile;
            $revision = new $className();
    
            $revision->preUp($this->_db);
    
            $file = tempnam(sys_get_temp_dir(), 'UpRevision');
            file_put_contents($file, $revision->upQuery);
            
            $config = $this->getServiceLocator()->get('Config');
            
            $cmd = "mysql -u {$config['db']['username']} --password={$config['db']['password']} -B {$config['db']['dbname']} < {$file}";
            exec($cmd, $output);
            unlink($file);
            
            $output = implode("\n", $output);
            if (!empty($output)) {
                return $output;
            }
    
            $revision->postUp($this->_db);
    
            $this->_saveRevision($number);
        } else {
            echo "Cannot load revision {$number}\n";
        }
    }
    
    protected function _downRevision($number)
    {
        $loadFile = $this->_rootPath . self::REVISIONS_PATH . $number . '.php';
        $className = self::CLASS_NAME . $number;
        if (is_readable($loadFile)) {
            include_once $loadFile;
            $revision = new $className();
    
            $revision->preDown($this->_db);
    
            $file = tempnam(sys_get_temp_dir(), 'DownRevision');
            file_put_contents($file, $revision->downQuery);

            $config = $this->getServiceLocator()->get('Config');
            
            $cmd = "mysql -u {$config['db']['username']} --password={$config['db']['password']} -B {$config['db']['dbname']} < {$file}";
            exec($cmd, $output);
            unlink($file);
            
            $output = implode("\n", $output);
            if (!empty($output)) {
                return $output;
            }
    
            $revision->postDown($this->_db);
    
            $this->_removeRevision($number);
        } else {
            echo "Cannot load revision {$number}\n";
        }
    }
}