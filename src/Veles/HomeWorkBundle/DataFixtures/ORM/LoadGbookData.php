<?php
    namespace Veles\HomeWorkBundle\DataFixtures\ORM;

    use Doctrine\Common\DataFixtures\AbstractFixture;
    use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
    use Doctrine\Common\Persistence\ObjectManager;
    use Symfony\Component\Yaml\Yaml;
    use Veles\HomeWorkBundle\Entity\Gbook;

    class LoadGbookData extends AbstractFixture implements OrderedFixtureInterface
    {
        /**
         * {@inheritDoc}
         */
        public function load(ObjectManager $manager)
        {
            $coments = Yaml::parse($this->getYmlFile());

            foreach ($coments['comments'] as $comentsKey => $comentsValue) {
                $gbook = new Gbook();

                $gbook
                    ->setName($comentsValue['name'])
                    ->setEmail($comentsValue['email'])
                    ->setMessage($comentsValue['message'])
                ;

                $manager->persist($gbook);
                $this->addReference($comentsKey, $gbook);
            }

            $manager->flush();
        }

        /**
         * {@inheritDoc}
         */
        public function getOrder()
        {
            return 1;
        }

        protected function getYmlFile()
        {
            return __DIR__ . '/appData/comments.yml';
        }
    }
?>