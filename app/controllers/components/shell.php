<?php


/**
 * 
 * @author ADRIAN TORRES
 * @package general
 * @subpackage components
 */

    class ShellComponent extends Object
    {
        /**
         * Controller
         *
         * @var object
         * @access public
         */
        public $controller = null;
        
        /**
         * Cake console
         *
         * @var string
         * @access public
         */
        public $cakeConsole = null;
        
        /**
         * Where php is
         *
         * @var string
         * @access public
         */
        public $php = '/usr/bin/php';
        
        
        /**
         * Startup controller
         *
         * @param object $controller Controller instance
         * @access public
         * @return void
         */
        public function startup(&$controller)
        {
            $this->controller = $controller;
            $this->cakeConsole = ROOT.DS.'cake'.DS.'console'.DS.'cake.php';
        }
        
        
        /**
         * Run shell command
         *
         * Example:
         * $this->Shell->run('myshell',array('task'=>'crawler','bg'=>true,'params'=>array('feedId'=>123,'batchId'=>345)));
         *
         * Options
         * - 'task' - Task to run
         * - 'params' - Params to pass to shell
         * - 'bg' - If script should run in background
         *
         * @param string $shell Shell command
         * @param array $options Options
         * @access public
         * @return string
         */
        public function run($shell,$options = array())
        {
            //Build command
            $cmd = array($shell);
            $append = '';
            $prepend = '';
            
            //Task
            if(isset($options['task']) && !empty($options['task'])) { $cmd[] = $options['task']; }
        
            //Params
            if(isset($options['params']) && !empty($options['params']))
            {
                foreach($options['params'] as $key => $val)
                {
                    $cmd[] = '-'.$key.' '.$val;
                }
            }

            //Background
            if(isset($options['bg']) && $options['bg'] == true)
            {
                $prepend = 'nohup ';
                $append = ' > /dev/null &';
            }
            
            $cmd = $prepend.$this->php.' '.$this->cakeConsole.' '.implode(' ',$cmd).$append;
            return exec($cmd);
        }

        
    }


?>