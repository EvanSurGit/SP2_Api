<?php

namespace Tests\Unit;

use App\Models\Puzzle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PuzzleTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function puzzle_can_be_read(): void
    {
        $puzzle = Puzzle::factory()->create([
            'nom'         => 'Test Puzzle',
            'categorie'   => 'Test Categorie',
            'description' => 'Ceci est un puzzle de test.',
            'prix'        => 9.99,
        ]);

        $found = Puzzle::find($puzzle->id);

        $this->assertNotNull($found);
        $this->assertSame($puzzle->id, $found->id);
        $this->assertSame('Test Puzzle', $found->nom);
        $this->assertSame('Test Categorie', $found->categorie);
    }

    /** @test */
    public function puzzle_can_be_updated(): void
    {
        $puzzle = Puzzle::factory()->create([
            'nom'       => 'Ancien nom',
            'categorie' => 'Ancienne categorie',
        ]);

        $updated = $puzzle->update([
            'nom'       => 'Nom mis à jour',
            'categorie' => 'Categorie mise à jour',
        ]);

        $this->assertTrue($updated);

        $this->assertDatabaseHas('puzzles', [
            'id'  => $puzzle->id,
            'nom' => 'Nom mis à jour',
        ]);

        $puzzle->refresh();
        $this->assertSame('Nom mis à jour', $puzzle->nom);
        $this->assertSame('Categorie mise à jour', $puzzle->categorie);
    }

    /** @test */
    public function puzzle_can_be_deleted(): void
    {
        $puzzle = Puzzle::factory()->create();

        $puzzle->delete();

        // Sans SoftDeletes :
        $this->assertDatabaseMissing('puzzles', ['id' => $puzzle->id]);
    }

    /** @test */
    public function deleting_a_nonexistent_puzzle_returns_zero(): void
    {
    $this->assertDatabaseCount('puzzles', 0);

    $nonExistentId = 999999; // ID qui n'existe pas
    $deletedCount = \App\Models\Puzzle::destroy($nonExistentId);

    $this->assertSame(0, $deletedCount);
    $this->assertDatabaseCount('puzzles', 0);
    }
}