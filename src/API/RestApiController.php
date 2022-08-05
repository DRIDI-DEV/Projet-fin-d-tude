<?php

namespace App\API;

use App\Entity\Document;
use App\Entity\Question;
use App\Entity\Quiz;
use App\Entity\Quizz;
use App\Entity\Reponse;
use App\Entity\Share;
use App\Entity\User;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Nelmio\ApiDocBundle\Annotation as Doc;


/**
 * @Route("/api")
 */
class RestApiController
{
    /**
     * @var
     */
    protected $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @Route("/list_document", name="api_list_document", methods={"GET"})
     *
     * @Doc\ApiDoc(
     *     resource=true,
     *     description="Get the list of all documents.",
     *      requirements={
     *         {
     *             "name"="published",
     *             "dataType"="boolean",
     *             "description"="if true we list the published documents only!"
     *         }
     *      }
     * )
     *
     * )
     *
     */
    public function listDocument(Request $request)
    {
        //@todo Here we need to check logged In user

        $isPublished = $request->query->get('published');

        if ($isPublished == 'true') {
            $allDocuments = $this->entityManager->getRepository(Document::class)->findBy(['published' => true]);
        } else {
            $allDocuments = $this->entityManager->getRepository(Document::class)->findAll();
        }

        $docArray = [];

        /** @var Document $document */
        foreach ($allDocuments as $document) {

            $docArray[]= [
                'title' => $document->getTitle(),
                'description' => $document->getDescription(),
                'date_creation' => $document->getCreationDate(),
                'date_update' => $document->getUpdatedDate(),
                'published' => $document->getPublished(),
                'image' => $document->getImage(),
                'author_id' => $document->getAuthorId(),
            ];
        }

        return new JsonResponse($docArray);
    }

    /**
     * @Route("/list_quiz", name="api_list_quiz", methods={"GET"})
     *
     * @Doc\ApiDoc(
     *     resource=true,
     *     description="Get the list of all quizz."
     * )
     */
    public function listQuiz(Request $request)
    {
        $allQuizz = $this->entityManager->getRepository(Quizz::class)->findAll();
        $result = [];

        /** @var Quizz $quiz */
        foreach ($allQuizz as $quiz) {
            $result [] = [
                'id' => $quiz->getId(),
                'name' =>$quiz->getNom(),
                'questions' => $this->builduestionResponse($quiz->getQuestions()),
            ];
        }

        return new JsonResponse($result);
    }

    public function builduestionResponse($questions)
    {
        $questionArray = [];
        /** @var Question $question */
        foreach ($questions as $question) {
            $questionArray [] = [
                'id' => $question->getId(),
                'titre' => $question->getQuestion(),
                'responses' => $this->buildResponse($question->getReponses())
            ];
        }
        return $questionArray;
    }

    public function buildResponse($responses)
    {
        $responseArray = [];
        /** @var Reponse $response */
        foreach ($responses as $response) {
            $responseArray [] = [
                'id' => $response->getId(),
                'titre' => $response->getReponse(),
                'correcte' => $response->getIsValid(),
            ];
        }

        return $responseArray;
    }

    /**
     * @Route("/share_doc", name="api_share_document", methods={"POST"})
     *
     * @Doc\ApiDoc(
     *     resource=true,
     *     description="This WS allows user to share a document."
     * )
     */
    public function shareDocument(Request $request)
    {
        $arrayData = json_decode($request->getContent(), true);
        if ($arrayData != null) {
            $arrayData = $arrayData[0];
        }

        /** @var User $user */
        $user = $this->entityManager->getRepository(User::class)->find($arrayData['sender_id']);
        if ($user == null) {

            return new JsonResponse("There is no user with ID : ". $arrayData['sender_id'], 400);
        }

        /** @var Document $document */
        $document = $this->entityManager->getRepository(Document::class)->find($arrayData['document_id']);
        if ($document == null) {

            return new JsonResponse("There is no document with ID : ". $arrayData['document_id'], 400);
        }

        if ($arrayData['email'] == null || $arrayData['email'] == '') {

            return new JsonResponse("Please provide a valid Email adresse", 400);
        }

        $share = new Share();
        $share->setDateTime(new \DateTime("now"));
        $share->setSenderId((int)$arrayData['sender_id']);
        $share->setSenderName($user->getNom() . ' ' . $user->getPrenom());
        $share->setDocumentId((int)$arrayData['document_id']);
        $share->setDocumentName($document->getTitle());
        $share->setEmail($arrayData['email']);

        $this->entityManager->persist($share);
        $this->entityManager->flush();

        return new JsonResponse("Email envoyé avec succès");
    }

}