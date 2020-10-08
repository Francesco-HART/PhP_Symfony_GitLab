<?php


namespace App\Services;

use App\Entity\Project;

class ProjectService
{
    public function convertProjectsGitlabToProject(array $gitlabProjects): array {
        $result = [];
        foreach ($gitlabProjects as $prj) {
            $project = new Project();
            $project->setGitlabId($prj['id']);
            $project->setName($prj['name']);
            $project->setDescription($prj['description']);
            $project->setCreatedAt(new \DateTime());
            array_push($result, $project);
        }
        return $result;
    }

}