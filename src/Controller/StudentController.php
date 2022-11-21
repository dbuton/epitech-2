<?php

namespace App\Controller;

use App\Entity\Student;
use App\Form\Model\StudentModel;
use App\Form\StudentType;
use App\Repository\StudentRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StudentController extends AbstractController
{
    #[Route('/student', name: 'app_student_list')]
    public function index(StudentRepository $repository): Response
    {
        $students = $repository->findAll();

        return $this->render('student/index.html.twig', [
            'students' => $students,
        ]);
    }

    #[Route('/student/{id}', name: 'app_student_display')]
    public function display($id, StudentRepository $repository): Response
    {
        $student = $repository->find($id);

        return $this->render('student/show.html.twig', [
            'student' => $student,
        ]);
    }

    #[Route('/student-add', name: 'app_student_add')]
    public function add(Request $request, StudentRepository $repository): Response
    {
        $student = new Student();
        $form = $this->createForm(StudentType::class, $student);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $repository->save($student, true);
            return $this->redirectToRoute('app_student_display', ['id' => $student->getId()]);
        }

        return $this->render('student/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
