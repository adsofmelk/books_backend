<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use App\Models\Book;
use Carbon\Factory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BooksApiTest extends TestCase
{
    use RefreshDatabase;

    // Testerar obtener todos loas libros
    /** @test */
    function can_get_all_books()
    {
        $books = Book::factory(10)->create();
        $this->getJson(route('books.index'))->assertJsonFragment([
            'title' => $books[0]->title
        ]);
    }


    /** @test */
    function can_get_one_book()
    {
        $book = Book::factory()->create();

        $this->getJson(route('books.show', $book))
            ->assertJsonFragment([
                'title' => $book->title
            ]);
    }

    /** @test */
    function can_create_books()
    {
        //verificamos error de validacion en el campo title, verifica que se este validando
        $this->postJson(route('books.store'), [])->assertJsonValidationErrorFor('title');

        $book = Book::factory()->create();
        $this->postJson('books.store', [
            'title' => $book->title
        ]);
        $this->assertDatabaseHas('books', [
            'title' => $book->title
        ]);
    }

    /** @test */
    function can_update_book()
    {
        $book = Book::factory()->create();

        //verificamos error de validacion en el campo title, verifica que se este validando
        $this->patchJson(route('books.update', $book), [])->assertJsonValidationErrorFor('title');


        $book->title = "Editado " . $book->title;

        $this->patchJson(route('books.update', $book), [
            'title' => $book->title
        ])->assertJsonFragment([
            'title' => $book->title
        ]);

        $this->assertDatabaseHas('books', [
            'title' => $book->title
        ]);
    }

    /** @test */
    function can_delete_books()
    {
        $book = Book::factory()->create();

        $this->deleteJson(route('books.destroy', $book))->assertNoContent();

        $this->assertDatabaseCount('books', 0); // despues de borrado no quedan libros

    }
}
