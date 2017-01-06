<?php

namespace Drupal\todo\Controller;


use Drupal\Core\Controller\ControllerBase;
use Drupal\Core\Database\Connection;
use Drupal\Core\Template\TwigEnvironment;
use Drupal\todo\DAO\TodoDAO;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;

class TodoController extends ControllerBase
{

    /**
     * @var TodoDAO
     */
    private $todoDao;

    /**
     * TodoController constructor.
     * @param $twig
     */
    public function __construct(TodoDAO $todoDAO)
    {
        /** @var Connection $db */
        $this->todoDao = $todoDAO;
    }

    public static function create(ContainerInterface $container) {
        return new static(
            $container->get('todo.dao.todo')
        );
    }

    public function listAction()
    {
        $todos = $this->todoDao->getAll();
        return [
            '#theme' => 'todo_list',
            '#todos' => $todos
        ];
    }

    public function detailAction($id)
    {
        $todo = $this->todoDao->get($id);
        if($todo === null) {
            throw new ResourceNotFoundException('Task not found...');
        }

        return [
            '#theme' => 'todo_details',
            '#todo' => $todo,
        ];
    }

    public function createAction(Request $request)
    {
        if(!$request->request->has('title')) {
            throw new \InvalidArgumentException('You should provide a title to create a todo');
        }

        $this->todoDao->create($request->request->get('title'));
        return $this->redirectToRoute('todo.list');
    }

    public function closeAction($id)
    {
        $this->todoDao->close($id);
        return $this->redirectToRoute('todo.list');
    }

    public function deleteAction($id)
    {
        $this->todoDao->delete($id);
        return $this->redirectToRoute('todo.list');
    }

    protected function redirectToRoute($routeName, $routeParameters=[])
    {
        return new RedirectResponse($this->url($routeName, $routeParameters));
    }

}