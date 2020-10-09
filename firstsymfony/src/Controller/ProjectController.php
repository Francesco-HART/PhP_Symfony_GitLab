<?php

namespace App\Controller;

use App\Entity\Project;
use App\Entity\Team;
use App\Form\ProjectType;
use App\Repository\ProjectRepository;
use App\Repository\TeamRepository;
use App\Services\GitLabApi;
use App\Services\ProjectService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/project")
 */
class ProjectController extends AbstractController
{
    /**
     * @Route("/", name="project_index", methods={"GET"})
     * @param ProjectRepository $projectRepository
     * @return Response
     */
    public function index(ProjectRepository $projectRepository): Response
    {
        return $this->render('project/index.html.twig', [
            'projects' => $projectRepository->findAll(),
            'isATeam' => false
        ]);
    }

    /**
     * @Route("/new", name="project_new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $project = new Project();
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('project_index');
        }

        return $this->render('project/new.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="project_show", methods={"GET"})
     * @param Project $project
     * @return Response
     */
    public function show(Project $project): Response
    {
        return $this->render('project/show.html.twig', [
            'project' => $project,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="project_edit", methods={"GET","POST"})
     * @param Request $request
     * @param Project $project
     * @return Response
     */
    public function edit(Request $request, Project $project): Response
    {
        $form = $this->createForm(ProjectType::class, $project);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('project_index');
        }

        return $this->render('project/edit.html.twig', [
            'project' => $project,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="project_delete", methods={"DELETE"})
     * @param Request $request
     * @param Project $project
     * @return Response
     */
    public function delete(Request $request, Project $project): Response
    {
        if ($this->isCsrfTokenValid('delete' . $project->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($project);
            $entityManager->flush();
        }

        return $this->redirectToRoute('project_index');
    }

    /**
     * @Route("/projects/load", name="project_load")
     * @param ProjectService $projectService
     * @param ProjectRepository $projectRepository
     * @param GitLabApi $gitLabApi
     * @return Response
     */
    public function load(ProjectService $projectService, ProjectRepository $projectRepository, GitLabApi $gitLabApi)
    {

        $gitlabProjects = $gitLabApi->fetch();
        $projects = $projectService->convertProjectsGitlabToProject($gitlabProjects);

        // get index of projects from gitlab
        $indexProjects = [];
        foreach ($projects as $item) {
            array_push($indexProjects, $item->getGitlabId());
        }

        // get index of projects from database
        $dbProjects = $projectRepository->findAll();
        $indexProjectInDataBase = [];
        foreach ($dbProjects as $item) {
            array_push($indexProjectInDataBase, $item->getGitlabId());
        }

        // part to add new project in database
        foreach ($projects as $project) {
            if (!in_array($project->getGitlabId(), $indexProjectInDataBase)) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($project);
                $entityManager->flush();
            }
        }

        // part to remove old project in database
        foreach ($dbProjects as $projectInDb) {
            if (!in_array($projectInDb->getGitlabId(), $indexProjects)) {
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->remove($projectInDb);
                $entityManager->flush();
            }
        }

        return $this->redirectToRoute('project_index');
    }

    /**
     * @Route("/{id}/merge-requests", name="merge_requests_show")
     * @param Request $req
     * @param GitLabApi $gitLabApi
     * @return Response
     */
    public function showMRByProject(Request $req, GitLabApi $gitLabApi)
    {
        $projectId = $req->get("id");
        $mergeRequests = $gitLabApi->fetchMRByProject($projectId);
        return $this->render('project/merge_requests.html.twig', [
            'mergeRequests' => $mergeRequests,
        ]);
    }

    /**
     * @Route("/{id}/projects", name="showProjectsByTeam", methods={"GET"})
     * @param Team $team
     * @return Response
     */
    public function showProjectsByTeam(Team $team)
    {
        return $this->render('project/index.html.twig', [
            'projects' => $team->getProject(),
            'isATeam' => true
        ]);
    }
}
