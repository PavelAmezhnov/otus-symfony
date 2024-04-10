<?php

namespace App\Repository;

use App\Entity\Course;
use App\Entity\Subscription;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryProxy;
use Doctrine\Common\Collections\AbstractLazyCollection;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Order;
use Doctrine\Common\Collections\Selectable;
use Doctrine\Persistence\ManagerRegistry;

class CourseRepository extends ServiceEntityRepositoryProxy
{

    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Course::class);
    }

    public function findByName(string $name): AbstractLazyCollection&Selectable
    {
        $criteria = Criteria::create()
            ->andWhere(Criteria::expr()->contains('name', $name))
            ->orderBy(['name' => Order::Ascending]);

        return $this->matching($criteria);
    }

    public function getSubscribedStudents(Course $course): Collection
    {
        $subscriptions = $course->getSubscriptions();
        $students = new ArrayCollection();

        /** @var Subscription $s */
        foreach ($subscriptions->toArray() as $s) {
            $students->add($s->getStudent());
        }

        return $students;
    }
}
