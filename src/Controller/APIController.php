<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Entity\User;

class APIController extends AbstractController
{
    /**
     * @Route("/users", name="get_users", methods={"GET"})
     */
    public function index()
    {
        $userCollection = $this->getDoctrine()->getRepository(User::class)->findAll();
        $arr = $this->userCollectionToArray($userCollection);

        return $this->json($arr);
    }

    private function userCollectionToArray(array $userCollection):array
    {
        $arr = [];
        foreach ($userCollection as $element) {
            $arr[] = [
                "firstname" => $element->getFirstname(),
                "surname" => $element->getSurname(),
                "identificationNumber" => $element->getIdentificationNumberPESEL(),
            ];
        }
        return $arr;
    }

    /**
     * @Route("/users", name="add_user", methods={"POST"})
     */
    public function add()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $errors = $this->validateData($data);
        if (!empty($errors)) {
            $response = new Response(
                json_encode($errors),
                $errors["code"],
                ['content-type' => 'application/json']
            );
            return $response;
        }

        $entityManager = $this->getDoctrine()->getManager();
        $user = new User($data["firstname"], $data["surname"], $data["identificationNumber"]);
        $entityManager->persist($user);
        $entityManager->flush();
        $response = new Response(
            json_encode($data),
            Response::HTTP_OK,
            ['content-type' => 'application/json']
        );
        return $response;
    }

    private function validateData(?array $data):array
    {
        $errors = [];
        if (gettype($data) != "array") {
            return [
                "code" => 400,
                "message" => "Bad request",
                "errors" => [
                    "JSON format" => "JSON is not valid."
                ]
            ];
        } else {
            if (!isset($data["firstname"])) {
                $errors["firstname"] = "Firstname must be specify.";
            }
            if (!isset($data["surname"])) {
                $errors["surname"] = "Surname must be specify.";
            }
            if (!isset($data["identificationNumber"])) {
                $errors["identificationNumber"] = "Identification number must be specify.";
            } elseif (!User::validateIdentificationNumberPesel($data["identificationNumber"])) {
                $errors["identificationNumber"] = "Invalid value for identificationNumber.";
            }
        }
        if (!empty($errors)) {
            return [
                "code" => 422,
                "message" => "Validation failed.",
                "errors" => $errors,
            ];
        } else {
            return [];
        }
    }

    /**
     * @Route("/", name="home")
     */
    public function home()
    {
        throw $this->createNotFoundException('Sorry, the page you are looking for could not be found.');
    }
}
