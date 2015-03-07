<?php
/*
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS
 * "AS IS" AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT
 * LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR
 * A PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT
 * OWNER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL,
 * SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT
 * LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE,
 * DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY
 * THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT
 * (INCLUDING NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE
 * OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * This software consists of voluntary contributions made by many individuals
 * and is licensed under the MIT license.
 */

namespace NoDbModule;

use Zend\ModuleManager\Feature\ConfigProviderInterface;
use Zend\ModuleManager\Feature\BootstrapListenerInterface;
use Zend\EventManager\EventInterface;
use Zend\Mvc\MvcEvent;

class Module implements ConfigProviderInterface, BootstrapListenerInterface
{

    public function onBootstrap(EventInterface $e)
    {
        $app = $e->getApplication();
        $eventManager = $app->getEventManager();

        $eventManager->attach('*', array($this, 'dbInstanceError'), 10000);
    }

    public function dbInstanceError(MvcEvent $e)
    {
        $application = $e->getTarget();
        $sm = $application->getServiceManager();

        //try to connect, and if not connected, then catch...
        try {
            /* @var $entityManager \Doctrine\ORM\EntityManager */
            $entityManager = $application->getServiceManager()
                ->get('Doctrine\ORM\EntityManager');

            $entityManager->getConnection()->connect();
        } catch (\Exception $e) {
            $viewModel = new \Zend\View\Model\ViewModel();
            $viewModel->setTemplate('error/dberror');

            echo $sm->get('ViewRenderer')->render($viewModel);
            //@todo LOG ERROR
            $e->stopPropagation();
        }
    }

    /**
     * {@InheritDoc}
     */
    public function getConfig()
    {
        return include __DIR__ . '/../config/module.config.php';
    }
}