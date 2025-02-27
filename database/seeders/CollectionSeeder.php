<?php

namespace Database\Seeders;

use Illuminate\Support\Facades\DB;
use Lunar\FieldTypes\Text;
use Lunar\FieldTypes\TranslatedText;
use Lunar\Models\Collection;
use Lunar\Models\CollectionGroup;

class CollectionSeeder extends AbstractSeeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $collections = $this->getSeedData('collections');

        $collectionGroup = CollectionGroup::first();

        // Ensure $collectionGroup is not null before proceeding
        if (! $collectionGroup) {
            $collectionGroup = CollectionGroup::create([
                'name' => 'Default Group',
                'handle' => 'default-group', // Ensure this field is provided
            ]);
        }

        DB::transaction(function () use ($collections, $collectionGroup) {
            foreach ($collections as $collection) {
                Collection::create([
                    'collection_group_id' => $collectionGroup->id,
                    'attribute_data' => [
                        'name' => new TranslatedText([
                            'en' => new Text($collection->name),
                        ]),
                        'description' => new TranslatedText([
                            'en' => new Text($collection->description),
                        ]),
                    ],
                ]);
            }
        });
    }
}
