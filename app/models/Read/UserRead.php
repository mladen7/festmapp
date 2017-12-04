<?php
namespace Models\Read;
/**
 * Created by PhpStorm.
 * User: Mladen
 * Date: 12/3/17
 * Time: 7:03 PM
 */
use Phalcon\Mvc\Model;
use Phalcon\Mvc\Model\Query;
use Models\User;

class UserRead extends Model {

    public $id;
    public $email;
    public $fb_id;
    public $google_id;
    public $name;
    public $image;

    public function executeRead()
    {
        $this->id = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->fb_id = $user->fb_id;
        $this->google_id = $user->google_id;
        $this->image = $user->image;

//        $listOfCommunities = array();
//        $community = array();
//        $firstMemberhip = array();

        foreach ($user->Member as $membership) {

            if ($membership->active != 0) {

                if (sizeof($firstMemberhip) == 0) {
                    $firstMembership['member_id'] = $membership->id;
                    $firstMembership['community_id'] = $membership->community_id;
                }

                $comm = Community::findFirst($membership->community_id);
                $community['member_id'] = $membership->id;
                $community['community_id'] = $membership->community_id;
                $community['is_manager'] = $membership->is_manager;
                $community['is_cashier'] = $membership->is_cashier;
                $community['active'] = $membership->active;
                $community['voting_points'] = $membership->voting_points;
                $community['div_description'] = $membership->div_description;

                $community['id'] = $comm->id;
                $community['latitude'] = $comm->latitude;
                $community['longitude'] = $comm->longitude;
                $community['address'] = $comm->address;
                $community['street_number'] = $comm->street_number;
                $community['address_id'] = $comm->address_id;
                $community['city'] = $comm->city;
                $community['community_type'] = $comm->type;
                $community['community_desc'] = $comm->description;
                $community['municipality_id'] = $comm->municipality_id;


                if ($membership->is_manager == 0) {
//                    $bind = array(
//                        'commid' => $membership->community_id
//                    );

//                    $memberManager = Member::query()
//                        ->where("community_id = :commid:")
//                        ->andWhere("is_manager=1")
//                        ->bind($bind)
//                        ->execute();

                    $memberManager = Member::findFirst(
                        [
                            "conditions" => "community_id = ?1 AND is_manager=1",
                            "bind"       => [
                                1 => $membership->community_id,
                            ]
                        ]
                    );

//                    $manager = User::findFirst($memberManager->user_id);
                    $manager = $memberManager->user;
                    $community['manager_id'] = $manager->id;
                    $community['manager_full_name'] = $manager->full_name;
                    $community['manager_profile_pic_URL'] = $manager->profile_pic_URL;
                    $community['manager_div_desc'] = $memberManager->div_description;
                } else {
                    $member = Member::findFirst(
                        [
                            "conditions" => "community_id = ?1 AND user_id = ?2",
                            "bind"       => [
                                1 => $membership->community_id,
                                2 => $user->id
                            ]
                        ]
                    );

                    $community['manager_id'] = $user->id;;
                    $community['manager_full_name'] = $user->full_name;
                    $community['manager_profile_pic_URL'] = $user->profile_pic_URL;
                    $community['manager_div_desc'] = $member->div_description;
                }

                array_push($listOfCommunities, $community);

            }
        }

        if (sizeof($listOfCommunities) != 0)
            $this->communities = $listOfCommunities;

        //return first membership data

    }

    function jsonSerialize()
    {
        $data = array();

        $data['id'] = $this->id;
        $data['name'] = $this->name;
        $data['email'] = $this->email;
        $data['fb_id'] = $this->fb_id;
        $data['google_id'] = $this->google_id;
        $data['image'] = $this->image;

        return $data;
    }
}