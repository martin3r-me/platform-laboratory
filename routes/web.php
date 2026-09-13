<?php

/**
 * Laboratory — web routes (Prefix 'laboratory' aus config).
 */

use Platform\Laboratory\Livewire\Index as LabTestIndex;

Route::get('/', LabTestIndex::class)->name('laboratory.index');
