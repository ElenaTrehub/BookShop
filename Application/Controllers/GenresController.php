<?php
/**
 * Created by PhpStorm.
 * User: elena
 * Date: 17.12.2018
 * Time: 16:09
 */

namespace Application\Controllers;


use Application\Services\GenresService;


class GenresController extends BaseController
{

    public function getGenresAction(){

        $genresService = new GenresService();

        $genres = $genresService->GetGenres();

        $template = $this->twig->load('Genre/genres-list.twig');

        $genreWithAmount = [];

        for($i=0;$i < count($genres); $i++ ){

            $genreWithAmount[$i] = [
                'genres' => $genres[$i],
                'amount' => $genresService->GetGenreBooksAmount($genres[$i]->genreID)
            ];

        }//for

        echo $template->render( array(
            'genres' =>  $genreWithAmount
        ) );
    }//getGenresAction

    public function getGenreAction( $id ){

        $genreService = new GenresService();

        $genre = $genreService->GetGenreByID( $id );

        $template = $this->twig->load('Genre/genre.twig');

        echo $template->render( array(
            'genre' => $genre
        ) );

    }//getAuthorAction

    public function updateGenreAction(){


        $id = $this->request->GetPostValue('id');
        $name = $this->request->GetPostValue('name');

        if (preg_match('/^[a-zA-Zа-яА-Я]/i', $name)) {
            $genresService = new GenresService();

            $result = $genresService->UpdateGenreByID($id, $name);

            if($result===true){
                $this->json( 200, array(
                    'status' => '200',
                    'genreID' => $id,
                ) );
            }//if
            else{

            }//if
        }//if
        else{
            $this->json( 400, array(
                'status' => '400',
                'genreID' => 0,
            ) );
        }

    }//updateGenreAction

    public function deleteGenreAction( $id ){

        $genreService = new GenresService();

        $genreService->DeleteGenreByID( $id );

        $this->json( 200 , array(
            'code' => 200,
            'authorID' => $id
        ) );
    }//deleteA

    public function addPostGenreAction(){

        $name = $this->request->GetPostValue('name');

        if (preg_match('/^[a-zA-Zа-яА-Я]/i', $name)) {
            $genresService = new GenresService();

            $result = $genresService->AddGenre( $name);

            $this->json( 200, array(
                'status' => '200',
                'genreID' => $result,
                'amount' => $genresService->GetGenreBooksAmount($result)
            ) );
        } else {
            $this->json( 400, array(
                'status' => '400',
                'genreID' => 0,
                'amount' => 0
            ) );
        }



       // echo json_encode( $myVar );



    }//addGenreAction
}//GenresController