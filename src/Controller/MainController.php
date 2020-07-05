<?php

namespace App\Controller;

use App\Entity\Image;
use App\Form\InformationFormType;
use App\Service\TextConverter;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class MainController extends AbstractController
{

    /**
     * @Route("/", name="app")
     */
    public function index()
    {
        return $this->render('main/index.html.twig');
    }

    /**
     * @Route("/main", name="app_main")
     * @throws Exception
     */
    public function main(EntityManagerInterface $em,
                         Request $request,
                         UserPasswordEncoderInterface $passwordEncoder,
                         TextConverter $converter)
    {
        $form = $this->createForm(InformationFormType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Image $image */
            $image = $form->getData();
            $uuid = $converter->convert($image->getText());
            $password = bin2hex(random_bytes(3));
            $link = $this->generateUrl('app_image_show', [
                'uuid' => $uuid,
            ], UrlGeneratorInterface::ABSOLUTE_URL);
            $image
                ->setUuid($uuid)
                ->setPassword($passwordEncoder->encodePassword($image, $password));
            try {
                $em->persist($image);
                $em->flush();
                $converter->save();

                $this->addFlash('generator', 'Image is successfully generated');
                $this->addFlash('link',
                    sprintf("Your link is <a href='%s' class='alert-link'>%s</a>", $link, $link)
                );
                $this->addFlash('password',
                    sprintf(
                        'Your password is <span id="copyTarget" class="font-weight-bold">%s</span> <button id="copyButton" class="btn btn-light btn-sm"><i class="fa fa-clipboard" aria-hidden="true"></i></button>',
                        $password
                    )
                );
                return $this->redirectToRoute('app_main');
            } catch (Exception $ex) {
                $this->addFlash('error', 'There are some problems with server!');
                return $this->render('main/main.html.twig', [
                    'form' => $form->createView(),
                ]);
            }
        }
        return $this->render('main/main.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
