<?php

namespace App\Controller\Admin;

use App\Controller\MediaController;
use App\Entity\Document;
use App\Entity\Media;
use App\Entity\Question;
use App\Entity\Share;
use App\Entity\Theme;
use App\Entity\User;
use App\Entity\Reponse;
use App\Entity\Quizz;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use phpDocumentor\Reflection\Types\Parent_;
use phpDocumentor\Reflection\Types\This;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use EasyCorp\Bundle\EasyAdminBundle\Config\UserMenu;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\HttpFoundation\File\File;
class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {

       $users = $this->getDoctrine()->getRepository(User::class)->findAll();
        $document = $this->getDoctrine()->getRepository(Document::class)->findAll();
        $quiz = $this->getDoctrine()->getRepository(Quizz::class)->findAll();
        $question = $this->getDoctrine()->getRepository(Question::class)->findAll();
        $theme = $this->getDoctrine()->getRepository(Theme::class)->findAll();
        $partage = $this->getDoctrine()->getRepository(Share::class)->findAll();
      //return parent::index();

        return
          $this->render('stats/index.html.twig', ['docNumber' => count($document), 'userNumber' => count($users), 'quizNumber' => count($quiz), 'questionNumber' => count($question), 'themeNumber' => count($theme), 'partageNumber' => count($partage)]);

         // parent::index();



    }

    public function configureDashboard(): Dashboard
    {

        return Dashboard::new()
            // the name visible to end users
            ->setTitle('Sales enablement')
            // you can include HTML contents too (e.g. to link to an image)
            ->setTitle('<img style="width: 40px; height: 40px;" src="../logo.png"> Sales <span class="text-small">Enablements</span>')

            // the path defined in this method is passed to the Twig asset() function
            //->setFaviconPath('favicon.png')

            // the domain used by default is 'messages'
            ->setTranslationDomain('my-custom-domain')

            // there's no need to define the "text direction" explicitly because
            // its default value is inferred dynamically from the user locale
            ->setTextDirection('ltr')

            // set this option if you prefer the page content to span the entire
            // browser width, instead of the default design which sets a max width
            ->renderContentMaximized()

            // set this option if you prefer the sidebar (which contains the main menu)
            // to be displayed as a narrow column instead of the default expanded design
           // ->renderSidebarMinimized()

            // by default, all backend URLs include a signature hash. If a user changes any
            // query parameter (to "hack" the backend) the signature won't match and EasyAdmin
            // triggers an error. If this causes any issue in your backend, call this method
            // to disable this feature and remove all URL signature checks
            ->disableUrlSignatures()
            ;


    }

  public function configureMenuItems(): iterable
    {


        return [
           MenuItem::linktoDashboard('Statistique', 'fa fa-bar-chart'),
            MenuItem::linkToCrud('Partage', 'fa fa-share', Share::class),

           MenuItem::section('Utilisateurs'),
            MenuItem::linkToCrud('Utilisateur', 'fa fa-users', User::class),

            MenuItem::section('Contenu'),
          //  MenuItem::linkToCrud('Media', 'fa fa-users', User::class),
            MenuItem::linkToCrud('Theme', 'fa fa-book', Theme::class),
            MenuItem::linkToCrud('Document', 'fa fa-file', Document::class),


            MenuItem::section('E-learning'),
            MenuItem::linkToCrud('Quizz', 'fa fa-puzzle-piece', Quizz::class),
            MenuItem::linkToCrud('Question', 'fa fa-question-circle', Question::class),
            MenuItem::linkToCrud('Reponse', 'fa fa-check', Reponse::class),





        ];


        //yield MenuItem::linkToCrud('Dashboard', 'fas fa-home', Media::class);



    }

    //public function configureUserMenu(UserInterface $user): UserMenu
  //  {
        // Usually it's better to call the parent method because that gives you a
        // user menu with some menu items already created ("sign out", "exit impersonation", etc.)
        // if you prefer to create the user menu from scratch, use: return UserMenu::new()->...
       // return parent::configureUserMenu($user)
            // use the given $user object to get the user name
           // ->setName($user->getPrenom())
            // use this method if you don't want to display the name of the user
            //->displayUserName(true)

            // you can return an URL with the avatar image
            //->setAvatarUrl('https://...')
           // ->setAvatarUrl($user->getImage())
            // use this method if you don't want to display the user image
            //->displayUserAvatar(true)
            // you can also pass an email address to use gravatar's service
          //  ->setGravatarEmail($user->getEmail())

            // you can use any type of menu item, except submenus
            //->addMenuItems([
                //MenuItem::linkToRoute('My Profile', 'fa fa-id-card', '...', ['...' => '...']),
                //MenuItem::linkToRoute('Settings', 'fa fa-user-cog', '...', ['...' => '...']),
                //MenuItem::section(),
                //MenuItem::linkToLogout('Logout', 'fa fa-sign-out'),
          //  ]);
   // }
}
