<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\AllSigned;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class IncrementChildrenCounterJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $creator;
    protected $newSignedChild;

    public function __construct(User $creator, $newSignedChild)
    {
        $this->creator = $creator;
        $this->newSignedChild = $newSignedChild;
    }

    public function handle()
    {
        while ($this->creator->parent) {
            $parent = User::find($this->creator->parent);

            $direction = $parent->left_user_id === $this->creator->id ? 'left' : 'right';

            AllSigned::create([
                'parent_id' => $parent->id,
                'child_id' => $this->newSignedChild,
                'direction' => $direction,
            ]);

            if ($direction === 'left') {
                $parent->left_children += 1;
            } else {
                $parent->right_children += 1;
            }

            $parent->save();
            $this->creator = $parent;
        }
    }
}
