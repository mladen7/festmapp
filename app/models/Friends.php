<?php
namespace Models;

class Friends extends \Models\ModelBase
{

    /**
     *
     * @var integer
     * @Primary
     * @Identity
     * @Column(type="integer", length=11, nullable=false)
     */
    public $id;

    /**
     *
     * @var string
     * @Column(type="string", length=40, nullable=false)
     */
    public $full_name;

    /**
     *
     * @var string
     * @Column(type="string", length=40, nullable=true)
     */
    public $nickname;

    /**
     *
     * @var string
     * @Column(type="string", length=40, nullable=false)
     */
    public $image;

    /**
     *
     * @var double
     * @Column(type="double", nullable=false)
     */
    public $location_lat;

    /**
     *
     * @var double
     * @Column(type="double", nullable=false)
     */
    public $location_lng;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $userId;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("Festmapp");
        $this->setSource("friends");
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'friends';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Friends[]|Friends
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Friends
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
