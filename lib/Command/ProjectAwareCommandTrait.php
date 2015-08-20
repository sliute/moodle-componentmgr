<?php

/**
 * Moodle component manager.
 *
 * @author Luke Carrier <luke@carrier.im>
 * @copyright 2015 Luke Carrier
 * @license GPL v3
 */

namespace ComponentManager\Command;

use ComponentManager\PlatformUtil;
use ComponentManager\Project;

/**
 * Project-aware command trait.
 *
 * Provides helpful utility methods for accessing the project in the currrent
 * working directory. Import this into command implementations to reduce
 * duplication.
 */
trait ProjectAwareCommandTrait {
    /**
     * Project.
     *
     * Lazily loaded -- be sure to call getProject() in order to ensure the
     * value is defined.
     *
     * @var \ComponentManager\Project
     */
    protected $project;

    /**
     * Get project.
     *
     * @return \ComponentManager\Project
     */
    protected function getProject() {
        if ($this->project === null) {
            $fileName = PlatformUtil::workingDirectory()
                      . PlatformUtil::directorySeparator()
                      . 'componentmgr.json';

            $packageRepositoryFactory = $this->container->get(
                    'package_repository.package_repository_factory');
            $packageSourceFactory = $this->container->get(
                    'package_source.package_source_factory');

            $this->logger->info('Parsing project file', [
                'filename' => $fileName,
            ]);
            $this->project = new Project($fileName, $packageRepositoryFactory,
                                         $packageSourceFactory);
        }

        return $this->project;
    }
}
