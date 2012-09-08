<?php

namespace Koios\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Koios\BlogBundle\Form\BlogType;
use Koios\BlogBundle\Entity\Blog;

/**
 * Admin controller.
 */
class AdminController extends Controller
{
    /**
     * Show list of blog entries
     * @return
     */
    public function indexAction()
    {
        $em = $this->getDoctrine()->getEntityManager();

        $blogs = $em->getRepository('KoiosBlogBundle:Blog')->findAll();

        if (0 == count($blogs)) {
            throw $this->createNotFoundException('Unable to find any Blog posts.');
        }

        return $this->render('KoiosBlogBundle:Admin:index.html.twig', array('blogs' => $blogs));
    }

    /**
     * Delete a selection of blog entries
     */
    public function deleteAction(Request $request)
    {
        $blog_ids = $request->get('blogs', false);

        if (is_array($blog_ids)) {
            $em = $this->getDoctrine()->getEntityManager();

            foreach($blog_ids as $id => $value) {
                $blog = $em->find('\Koios\BlogBundle\Entity\Blog', $id);
                if ( null !== $blog ) {
                    $em->remove($blog);
                }
            }
            $em->flush();
            $this->get('session')->setFlash('blogger-notice', 'The selected blog entries have been successfully deleted.');
        } else {
            $this->get('session')->setFlash('blogger-error', 'You must select at least 1 valid blog.  No blog posts were deleted.');
        }

        return $this->redirect($this->generateUrl('KoiosBlogBundle_admin'));

    }

    public function createAction(Request $request) {
        $blog = new Blog();

        $form = $this->createForm(new BlogType(), $blog);

        if('POST' == $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $blog->setAuthor($this->get('security.context')->getToken()->getUser()->getUsername());

                $em = $this->getDoctrine()->getEntityManager();
                $em->persist($blog);
                $em->flush();

                $this->get('session')->setFlash('blogger-notice', 'Post create updated!');
                return $this->redirect($this->generateUrl('KoiosBlogBundle_admin'));
            }
        }

        return $this->render('KoiosBlogBundle:Admin:create.html.twig', array('form' => $form->createView()));
    }

    /**
     * @param Request $request
     *
     * @return Response
     */
    public function editAction(Request $request)
    {
        $em = $this->getDoctrine()->getEntityManager();

        $blog = $em->getRepository('KoiosBlogBundle:Blog')->find($request->get('id'));

        if (!$blog) {
            throw $this->createNotFoundException('Unable to find Blog post.');
        }

        $form = $this->createForm(new BlogType(), $blog);

        if('POST' == $request->getMethod()) {
            $form->bindRequest($request);

            if ($form->isValid()) {
                $em->persist($blog);
                $em->flush();

                $this->get('session')->setFlash('blogger-notice', 'Post successfully updated!');
                return $this->redirect($this->generateUrl('KoiosBlogBundle_admin_blog_edit', array('id' => $blog->getId())));
            }
        }

        return $this->render('KoiosBlogBundle:Admin:edit.html.twig', array('blog' => $blog, 'form' => $form->createView()));
    }
}
