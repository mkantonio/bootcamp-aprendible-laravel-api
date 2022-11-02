<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BooksApiTest extends TestCase
{
    use RefreshDatabase;
    /** @test
     * test_can_get_all_books() : alternative name
     */
    function can_get_all_books(){
        $books = Book::factory(4)->create();
//        $this->get('/api/books')->dump();
        $response = $this->getJson(route('books.index'));
        $response->assertJsonFragment([
            'title' => $books[0]->title
        ])->assertJsonFragment([
            'title' => $books[1]->title
        ]);

//        dd($books->count());
    }
    /** @test */
    function can_get_one_book(){
        $book = Book::factory()->create();

//        dd(route('books.show', $book));
        $response = $this->getJson(route('books.show', $book));
        $response->assertJsonFragment([
            'title' => $book->title,
        ]);
    }


    /** @test */
    function can_create_books(){

        // Validación del método Validate en books.store
        $this->postJson(route('books.store'), [])
            -> assertJsonValidationErrorFor('title');

        // Verificar si crea y devuelve el registro creado
        $this->postJson(route('books.store'), [
            'title' => 'My new book'

        ])->assertJsonFragment([
            'title' => 'My new book'
        ]);

        // Verificar si existe el registro en la BD
        $this->assertDatabaseHas('books', [
            'title' => 'My new book'
        ]);
    }

    /** @test */
    function can_update_books(){

        // Creación del libro
        $book = Book::factory()->create();

        // Validación del método Validate en books.update
        $this->patchJson(route('books.update', $book), [])
            -> assertJsonValidationErrorFor('title');

        // Update del libro y esperar que devuelva lo que se editó
        $this->patchJson(route('books.update', $book), [
            'title' => 'Edited book'
        ])->assertJsonFragment([
            'title' => 'Edited book'
        ]);

        // Verificar si existe el registro en la BD
        $this->assertDatabaseHas('books', [
            'title' => 'Edited book'
        ]);
    }


    /** @test */
    function can_delete_books(){

        $book = Book::factory()->create();

        $this->deleteJson(route('books.destroy', $book))
            ->assertNoContent();

        $this->assertDatabaseCount('books', 0);
    }
}
