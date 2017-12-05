<?php
namespace Controllers;

use Exceptions\RestParameterNotFoundException;
use Models\Artist;
use Phalcon\Mvc\Controller;
use Phalcon\Http\Response;

class ArtistController extends ControllerBase {

    /**
     **
     * @ApiDescription(section="Artist", description="Find artist by id")
     * @ApiMethod(type="post")
     * @ApiRoute(name="/artist/get-artist")
     * @ApiParams(name="artist_id", type="integer", nullable=false, description="Artist id")
     * @ApiReturnHeaders(sample="HTTP 200 OK")
     * @ApiReturn(type="object", sample=" {
    'id': '4',
    'name': 'Nikola Nikolic',
    'country': 'Serbia'
     * }
     * ")
     */
    public function getArtistById()
    {
        $response = $this->response;

        try {
            $id = $this->getJsonParamFromPOST('artist_id','int',false);
                if ($id) {
                    $artist = Artist::findFirst(
                    [
                        "id = :id:",
                        "bind" => [
                            "id" => $id
                        ]
                    ]
                );

                $response->setStatusCode(200, "OK");
                if ($artist) {
                    $response->setJsonContent($artist);
                } else {
                    $response->setJsonContent([]);
                }

            } else {
                    $artists = Artist::find();
                    $response->setStatusCode(200, "OK");
                    if ($artists) {
                        $response->setJsonContent($artists);

                    } else {
                        $response->setJsonContent([]);

                    }
            }
        } catch(RestParameterNotFoundException $r) {
            $response = $r->getPhalconResponse();
        } catch (\Exception $e) {
            $response->setStatusCode(500, "Unexpected error");
            $response->setJsonContent(array('status' => 'Greška', 'messages' => 'Došlo je do neočekivane greške. Molimo pokušajte ponovo i/ili kontaktirajte nas ukoliko se problem ne otkloni.'));
        } finally {
            return $response;
        }
    }

}

