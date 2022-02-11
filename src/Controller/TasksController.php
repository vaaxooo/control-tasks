<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Repository\TaskRepository;
use App\Entity\Task;
use Symfony\Component\HttpFoundation\Request;

class TasksController extends AbstractController
{
    /**
     * @Route("/", name="tasks")
     */
    public function index(Request $request): Response
    {
        $rules = [];
        $params = $request->query;

        $query = $this->getDoctrine()
                    ->getRepository(Task::class)
                    ->createQueryBuilder('t');
        if($params->get('search') && !empty($params->get('search'))) {
            $query->where('t.description LIKE :description')
                ->setParameter('description', '%'.$params->get('search').'%');
        }

        if($params->get('start') && !empty($params->get('start'))) {
            if($params->get('end') && !empty($params->get('end'))) {
                $query->andWhere('t.start >= :startDate')
                ->setParameter('startDate', $params->get('start'));
                $query->andWhere('t.start <= :endDate')
                ->setParameter('endDate', $params->get('end'));
            } else {
                $query->andWhere('t.start >= :startDate')
                ->setParameter('startDate', $params->get('start'));
                $query->andWhere('t.start <= :endDate')
                ->setParameter('endDate', strtotime("+1 day"));
            }
        }

        if($params->get('end') && !empty($params->get('end') && !$params->get('start'))) {
            $query->andWhere('t.start <= :endDate')
            ->setParameter('endDate', $params->get('end'));
        }


        $tasks = $query->getQuery()->getResult();

        return $this->render('tasks/index.html.twig', [
            'tasks' => $tasks,
            'params' => $params->all()
        ]);
    }

    /**
     * @Route("/tasks/{id}/delete", name="tasks_delete")
     */
    public function delete($id): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $task = $entityManager->getRepository(Task::class)->find($id);
        $entityManager->remove($task);
        $entityManager->flush();
        return $this->redirectToRoute("tasks");
    }

    /**
     * @Route("/tasks/{id}/edit", name="tasks_edit")
     */
    public function edit(Request $request, $id): Response
    {
        $task = $this->getDoctrine()
                    ->getRepository(Task::class)
                    ->find($id);
        if($request->isMethod("POST")) {
            $params = $request->request;
            $entityManager = $this->getDoctrine()->getManager();
            $task->setStart($params->get('start'));
            $task->setEnd($params->get('end'));
            $task->setDescription($params->get('description'));
            $task->setReason($params->get('reason'));
            $entityManager->persist($task);
            $entityManager->flush();
            return $this->redirectToRoute("tasks");
        }
        return $this->render('tasks/edit.html.twig', ['task' => $task]);
    }


    /**
     * @Route("/tasks/add", name="tasks_add")
     */
    public function add(Request $request): Response
    {
        if($request->isMethod("POST")) {
            $params = $request->request;
            $entityManager = $this->getDoctrine()->getManager();
            $task = new Task();
            $task->setStart($params->get('start'));
            $task->setEnd($params->get('end'));
            $task->setDescription($params->get('description'));
            $task->setReason($params->get('reason'));
            $entityManager->persist($task);
            $entityManager->flush();
            return $this->redirectToRoute("tasks");
        }
        return $this->render('tasks/add.html.twig', []);
    }

}

