<?php

namespace Creode\Csmt\Command\Snapshot\Modules;

use Creode\Csmt\Command\Snapshot\SnapshotTaker;

class SnapshotCommand extends SnapshotTaker
{
    protected function configure()
    {
        $this->setName('snapshot:modules');
        $this->setDescription('Takes a modules snapshot');
    }

    public function takeSnapshot()
    {
       $projectConfig = $this->_config->get('project');
	   $framework = $projectConfig['framework'];
	   $modules = array();

	   if($framework == 'drupal7') {
	   		$modules = $this->getDrupal7Modules();
	   }

	   echo print_r($modules, true);
	   echo "\n";
    }

	private function getDrupal7Modules() {
		$moduleConfig = $this->_config->get('modules');
		$modules = array();

		if($moduleConfig) {
			$moduleDirs = $moduleConfig['moduledir'];

			foreach($moduleDirs as $moduleDir) {
				$modules = array_merge($modules, $this->getDrupal7ModulesFromDir($moduleDir));
			}

			return $modules;
		}
		
		return false;
	}

	private function getDrupal7ModulesFromDir($moduleDir) {
		$modules = array();
		$directories = scandir($moduleDir);

		foreach($directories as $directory) {
			$module = array(
				'name' => $directory
			);

			// get version from info file
			if($directory != '.' && $directory != '..' && strpos($directory, '.') == false) {
				$infoFilePath = $moduleDir . '/' . $directory . '/' . $directory . '.info';
				$infoFile = fopen($infoFilePath, 'r');

				if($infoFile) {
					while(($line = fgets($infoFile)) !== false) {
						if(strpos($line, 'version = ') !== false) {
							$module['version'] = str_replace('version = ', '', $line);
							$module['version'] = str_replace('"', '', $module['version']);
						}
					}
					fclose($infoFile);
				}
			}
			array_push($modules, $module);
		}

		return $modules;
	}
}
