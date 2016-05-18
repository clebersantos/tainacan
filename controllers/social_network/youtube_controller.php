<?php

require_once(dirname(__FILE__) . '../../../models/social_network/youtube_model.php');
require_once(dirname(__FILE__) . '../../../models/object/object_model.php');
require_once(dirname(__FILE__) . '../../general/general_controller.php');

class YoutubeController extends Controller {

    public function operation($operation, $data) {
        switch ($operation) {
            //BEGIN New Code
            case "import_video_url":
                // recupera a chave da api cadastrada
                $config = get_option('socialdb_theme_options');
                $urlVideoYoutube = explode('/', $data['video_url']);
                $videoID = explode('=', $urlVideoYoutube[3]);
                if ($videoID[1]) {
                    $youtube = new youtubeModel($videoID[1], $config, false, true);
                    $object_model = new ObjectModel();
                    return $youtube->insertVideoItem($data, $object_model);
                } else {
                    return false;
                }
                break;
            case "import_video_channel":
                // recupera a chave da api cadastrada
                $config = get_option('socialdb_theme_options');

                if ($data['playlist'] == '') {
                    //verifica se o identificador do canal passado é uma URL
                    $urlBaseYoutube = explode('/', $data['identifier']);
                    if ($urlBaseYoutube[0] == 'https:' && $urlBaseYoutube[2] == 'www.youtube.com') {
                        $tipo = $urlBaseYoutube[3];
                        if ($tipo == 'user') {
                            $user = $urlBaseYoutube[4];
                            $youtube = new YoutubeModel($user, $config, true);
                        } elseif ($tipo == 'channel') {
                            $channel = $urlBaseYoutube[4];
                            $youtube = new youtubeModel($channel, $config, false);
                        } else {
                            return false;
                        }
                    } else {
                        //tenta instanciar objeto através do nome do usuário do canal
                        $youtube = new YoutubeModel($data['identifier'], $config, true);
                        //caso falhe, intancia objeto pelo id do canal
                        if (!$youtube) {
                            $youtube = new YoutubeModel($data['identifier'], $config, false);
                            if (!$youtube) {
                                return false;
                            }
                        }
                    } // termina o tratamento de urls
                }else{
                    $youtube = new YoutubeModel('', $config, false, false, true);
                }

                $object_model = new ObjectModel();
                $return = $youtube->insertVideoChannel($data, $object_model);
                if ($return) {
                    return json_encode($return);
                } else {
                    return json_encode([]);
                }
                break;
            //END New Code
            //*********************************************************************************************//
            case "getVideosYoutube":
                // recupera a chave da api cadastrada
                $config = get_option('socialdb_theme_options');
                //verifica se o identificador do canal passado é uma URL
                $urlBaseYoutube = explode('/', $data['identifier']);
                if ($urlBaseYoutube[0] == 'https:' && $urlBaseYoutube[2] == 'www.youtube.com') {
                    $tipo = $urlBaseYoutube[3];
                    if ($tipo == 'user') {
                        $user = $urlBaseYoutube[4];
                        $youtube = new YoutubeModel($user, $config, true);
                    } elseif ($tipo == 'channel') {
                        $channel = $urlBaseYoutube[4];
                        $youtube = new youtubeModel($channel, $config, false);
                    } else {
                        return false;
                    }
                } else {
                    //tenta instanciar objeto através do nome do usuário do canal
                    $youtube = new YoutubeModel($data['identifier'], $config, true);
                    //caso falhe, intancia objeto pelo id do canal
                    if (!$youtube) {
                        $youtube = new YoutubeModel($data['identifier'], $config, false);
                        if (!$youtube) {
                            return false;
                        }
                    }
                } // termina o tratamento de urls

                $object_model = new ObjectModel();
                $data['playlist'] = ($data['playlist'] == __('Get all videos, no playlist specified.', 'tainacan') ? '' : $data['playlist']);
                return $youtube->getAllVideos($data, $object_model);

                break;

            case "updateVideosYoutube":
                // recupera a chave da api cadastrada
                $config = get_option('socialdb_theme_options');
                //verifica se o identificador do canal passado é uma URL
                $urlBaseYoutube = explode('/', $data['identifier']);
                if ($urlBaseYoutube[0] == 'https:' && $urlBaseYoutube[2] == 'www.youtube.com') {
                    $tipo = $urlBaseYoutube[3];
                    if ($tipo == 'user') {
                        $user = $urlBaseYoutube[4];
                        $youtube = new YoutubeModel($user, $config, true);
                    } elseif ($tipo == 'channel') {
                        $channel = $urlBaseYoutube[4];
                        $youtube = new youtubeModel($channel, $config, false);
                    } else {
                        return false;
                    }
                } else {
                    //tenta instanciar objeto através do nome do usuário do canal
                    $youtube = new YoutubeModel($data['identifier'], $config, true);
                    //caso falhe, intancia objeto pelo id do canal
                    if (!$youtube) {
                        $youtube = new YoutubeModel($data['identifier'], $config, false);
                        if (!$youtube) {
                            return false;
                        }
                    }
                } // termina o tratamento de url

                $object_model = new ObjectModel();
                $data['playlist'] = ($data['playlist'] == __('Get all videos, no playlist specified.', 'tainacan') ? '' : $data['playlist']);
                return $youtube->updateVideosChannel($data, $object_model);
                //return $youtube->getAllVideosUploaded($data, $object_model);

                break;

            case "listIdentifiersYoutube":
                return YoutubeModel::list_channels($data['collectionId']);
                break;

            case "deleteIdentifierYoutube":

                return YoutubeModel::delete_channel($data['identifier'], $data['collection_id']);
                break;

            case "editIdentifierYoutube":

                return YoutubeModel::edit_channel($data['identifier'], $data['new_identifier'], $data['new_playlist']);
                break;

            case "InsertIdentifierYoutube":
                return YoutubeModel::insert_channel($data['identifier'], $data['playlist'], $data['collectionId']);
                break;

            case "list":
                return $this->render(dirname(__FILE__) . '../../../views/social_network/list.php', $data);
                break;
        }
    }

}

if ($_POST['operation']) {
    $operation = $_POST['operation'];
    $data = $_POST;
} else {
    $operation = $_GET['operation'];
    $data = $_GET;
}

$youtube_controller = new YoutubeController();
echo $youtube_controller->operation($operation, $data);
?>