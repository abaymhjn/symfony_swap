<?php
// src/Entity/History.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="history")
 */
class History
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", name="first_in_value")
     */
    private $firstIn;

    /**
     * @ORM\Column(type="integer", name="second_in_value")
     */
    private $secondIn;

    /**
     * @ORM\Column(type="integer", name="first_out_value")
     */
    private $firstOut;

    /**
     * @ORM\Column(type="integer", name="second_out_value")
     */
    private $secondOut;

    /**
     * @ORM\Column(type="datetime", name="creation_date")
     */
    private $creationDate;

    /**
     * @ORM\Column(type="datetime", name="update_date")
     */
    private $updateDate;

    // Add getters and setters for each property
}
