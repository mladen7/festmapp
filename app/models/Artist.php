<?php
namespace Models;

class Artist extends \Models\ModelBase{

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
    public $name;

    /**
     *
     * @var string
     * @Column(type="string", length=40, nullable=false)
     */
    public $country;

    /**
     *
     * @var string
     * @Column(type="string", length=70, nullable=true)
     */
    public $linkToSong;

    /**
     *
     * @var string
     * @Column(type="string", length=40, nullable=true)
     */
    public $linkInstagram;

    /**
     *
     * @var string
     * @Column(type="string", length=40, nullable=true)
     */
    public $linkFacebook;

    /**
     *
     * @var string
     * @Column(type="string", length=40, nullable=true)
     */
    public $linkTwitter;

    /**
     *
     * @var string
     * @Column(type="string", length=40, nullable=true)
     */
    public $linkYoutube;

    /**
     *
     * @var string
     * @Column(type="string", length=40, nullable=true)
     */
    public $linkSoundCloud;

    /**
     *
     * @var string
     * @Column(type="string", length=40, nullable=true)
     */
    public $linkWebsite;

    /**
     *
     * @var integer
     * @Column(type="integer", length=11, nullable=false)
     */
    public $stageID;

    /**
     *
     * @var string
     * @Column(type="string", nullable=false)
     */
    public $date;

    /**
     *
     * @var string
     * @Column(type="string", length=70, nullable=false)
     */
    public $imageThumbnail;

    /**
     *
     * @var string
     * @Column(type="string", length=70, nullable=false)
     */
    public $imageAvatar;

    /**
     *
     * @var integer
     * @Column(type="integer", length=4, nullable=false)
     */
    public $stageHeadliner;

    /**
     * Initialize method for model.
     */
    public function initialize()
    {
        $this->setSchema("Festmapp");
        $this->setSource("artist");
//        $this->hasMany('id', 'Models\Community', 'address_id', ['alias' => 'Community']);
    }

    /**
     * Returns table name mapped in the model.
     *
     * @return string
     */
    public function getSource()
    {
        return 'artist';
    }

    /**
     * Allows to query a set of records that match the specified conditions
     *
     * @param mixed $parameters
     * @return Artist[]|Artist
     */
    public static function find($parameters = null)
    {
        return parent::find($parameters);
    }

    /**
     * Allows to query the first record that match the specified conditions
     *
     * @param mixed $parameters
     * @return Artist
     */
    public static function findFirst($parameters = null)
    {
        return parent::findFirst($parameters);
    }

}
