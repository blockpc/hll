<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Map;
use Illuminate\Database\Seeder;

final class MapSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /**
         * @var array<int, array{
         *     alias: string,
         *     name: string,
         *     timeline: string,
         *     location: string,
         *     description: string,
         *     points: array<int, string>
         * }>
         */
        $maps = [
            [
                'alias' => 'alamein',
                'name' => 'El Alamein',
                'timeline' => '1942',
                'location' => 'North Africa. El Alamein, Egipto',
                'description' => 'The Battle of El Alamein was a major turning point in the North African campaign of World War II. It took place in late 1942 and marked the beginning of the end for the Axis forces in North Africa.',
                'points' => ['Valley', 'Oasis', 'Desert Rat Trenches'],
            ],
            [
                'alias' => 'tobruk',
                'name' => 'Tobruk',
                'timeline' => '1942',
                'location' => 'North Africa. Tobruk, Libia',
                'description' => 'The Siege of Tobruk was a prolonged military engagement during World War II, where Allied forces held the Libyan port city of Tobruk against Axis forces for over 240 days in 1941.',
                'points' => ['Admiralty House', 'Church Grounds', 'Desert Rat Caves'],
            ],
            [
                'alias' => 'stalingrad',
                'name' => 'Stalingrad',
                'timeline' => '1942 - 1943',
                'location' => 'Eastern Front. Volgogrado (Stalingrado), Soviet Union',
                'description' => 'The Battle of Stalingrad was a major battle on the Eastern Front of World War II, where the Soviet Union successfully defended the city of Stalingrad against the German Army, marking a turning point in the war.',
                'points' => ['Train Station', 'Carriage Depot', 'Railway Crossing'],
            ],
            [
                'alias' => 'kursk',
                'name' => 'Kursk',
                'timeline' => '1943',
                'location' => 'Eastern Front. Kursk, Soviet Union',
                'description' => 'The Battle of Kursk was a significant battle on the Eastern Front of World War II, where the Soviet Union successfully defended against a major German offensive, marking a turning point in the war.',
                'points' => ['The Windmills', 'Yamki', 'Olegs House'],
            ],
            [
                'alias' => 'kharkov',
                'name' => 'Kharkov',
                'timeline' => '1943',
                'location' => 'Eastern Front. Járkov, Soviet Union',
                'description' => 'The Battle of Kharkov was a series of battles on the Eastern Front of World War II, where the German Army launched multiple offensives to capture the city of Kharkov, but ultimately failed to secure a decisive victory.',
                'points' => ['Water Mill', 'St Mary', 'Distillery'],
            ],
            [
                'alias' => 'smolensk',
                'name' => 'Smolensk',
                'timeline' => '1943',
                'location' => 'Eastern Front. Smolensk, Soviet Union',
                'description' => 'The Battle of Smolensk was a significant battle on the Eastern Front of World War II, where the Soviet Union successfully defended the city of Smolensk against the German Army, marking a turning point in the war.',
                'points' => ['84th Battalion Bridge', 'Zhelyabova Square', 'Pyatnitskii Overpass'],
            ],
            [
                'alias' => 'omaha',
                'name' => 'Omaha Beach',
                'timeline' => '1944 (junio)',
                'location' => 'Western Front. Omaha Beach, Normandy, France',
                'description' => 'Omaha Beach was one of the five landing beaches during the D-Day invasion of Normandy in World War II. It was heavily defended by German forces, resulting in significant casualties for the Allied forces, but ultimately contributed to the success of the invasion.',
                'points' => ['Artillery Battery', 'Vierville Sur Mer', 'West Vierville'],
            ],
            [
                'alias' => 'utah',
                'name' => 'Utah Beach',
                'timeline' => '1944 (junio)',
                'location' => 'Western Front. Utah Beach, Normandy, France',
                'description' => 'Utah Beach was one of the five landing beaches during the D-Day invasion of Normandy in World War II. It was the westernmost landing site and was less heavily defended than Omaha Beach, resulting in fewer casualties for the Allied forces and contributing to the success of the invasion.',
                'points' => ['WN7', 'The Chapel', 'WN4'],
            ],
            [
                'alias' => 'sme',
                'name' => 'Sainte-Mère-Église',
                'timeline' => '1944 (junio)',
                'location' => 'Western Front. Sainte-Mère-Église, Normandy, France',
                'description' => 'Sainte-Mère-Église was one of the first towns liberated by the Allied forces during the D-Day invasion of Normandy in World War II. It was heavily defended by German forces, but ultimately contributed to the success of the invasion and became a symbol of the bravery and sacrifice of the Allied forces.',
                'points' => ['Checkpoint', 'Ste Mere Eglise', 'Hospice'],
            ],
            [
                'alias' => 'smdm',
                'name' => 'Sainte-Marie-du-Mont',
                'timeline' => '1944 (junio)',
                'location' => 'Western Front. Sainte-Marie-du-Mont, Normandy, France',
                'description' => 'Sainte-Marie-du-Mont was one of the landing sites during the D-Day invasion of Normandy in World War II. It was heavily defended by German forces, but ultimately contributed to the success of the invasion and became a symbol of the bravery and sacrifice of the Allied forces.',
                'points' => ['The Dugout', 'AA Network', 'Pierres Farm'],
            ],
            [
                'alias' => 'carentan',
                'name' => 'Carentan',
                'timeline' => '1944 (junio - julio)',
                'location' => 'Western Front. Carentan, Normandy, France',
                'description' => 'Carentan was a strategic town during the D-Day invasion of Normandy in World War II. It was heavily defended by German forces, but ultimately contributed to the success of the invasion and became a symbol of the bravery and sacrifice of the Allied forces.',
                'points' => ['Train Station', 'Town Center', 'Canal Crossing'],
            ],
            [
                'alias' => 'phl',
                'name' => 'Purple Heart Lane',
                'timeline' => '1944 (verano)',
                'location' => 'Western Front. Purple Heart Lane, Normandy, France',
                'description' => 'Purple Heart Lane was a section of the battlefield during the D-Day invasion of Normandy in World War II. It was heavily defended by German forces, resulting in significant casualties for the Allied forces, but ultimately contributed to the success of the invasion and became a symbol of the bravery and sacrifice of the Allied forces.',
                'points' => ['Grout Pillbox', 'Carentan Causeway', 'Flak Position'],
            ],
            [
                'alias' => 'mortain',
                'name' => 'Mortain',
                'timeline' => '1944 (agosto)',
                'location' => 'Western Front. Mortain, Normandy, France',
                'description' => 'The Battle of Mortain was a significant battle during the Normandy campaign of World War II. It took place in August 1944 and was an attempt by German forces to cut off the Allied forces advancing from the beaches. The battle resulted in a decisive victory for the Allied forces and contributed to the eventual liberation of France.',
                'points' => ['US Southern Roadblock', 'Petit Chappelle Saint Michelle', 'Hill 314'],
            ],
            [
                'alias' => 'driel',
                'name' => 'Driel',
                'timeline' => '1944 (septiembre)',
                'location' => 'Western Front. Driel, Netherlands',
                'description' => 'The Battle of Driel was a significant battle during the Operation Market Garden campaign of World War II. It took place in September 1944 and was an attempt by Allied forces to secure a crossing over the Rhine River. The battle resulted in a German victory and contributed to the failure of the overall operation.',
                'points' => ['Brick factory', 'Railway Brigde', 'Gun Emplacements'],
            ],
            [
                'alias' => 'hf',
                'name' => 'Hürtgen Forest',
                'timeline' => '1944 (septiembre - diciembre)',
                'location' => 'Western Front. Hürtgen Forest, Germany',
                'description' => 'The Battle of Hürtgen Forest was a prolonged and costly battle during World War II, taking place from September to December 1944. It was fought between American and German forces in the dense Hürtgen Forest along the German-Belgian border. The battle resulted in heavy casualties for both sides and is considered one of the longest battles on German soil during the war.',
                'points' => ['The Siegfried Line', 'The Scar', 'North Pass'],
            ],
            [
                'alias' => 'h400',
                'name' => 'Hill 400',
                'timeline' => '1944 (octubre)',
                'location' => 'Western Front. Hill 400, Hürtgen Forest, Germany',
                'description' => 'The Battle of Hill 400 was a significant engagement during the Battle of Hürtgen Forest in World War II. It took place in October 1944 and was fought between American and German forces for control of a strategic hill in the dense forest. The battle resulted in heavy casualties for both sides and is remembered for the intense fighting and difficult conditions faced by the soldiers.',
                'points' => ['Southern Approach', 'Hill 400', 'Flak Pits'],
            ],
            [
                'alias' => 'foy',
                'name' => 'Foy',
                'timeline' => '1944 (diciembre)',
                'location' => 'Western Front. Foy, Belgium',
                'description' => 'The Battle of Foy was a significant engagement during the Battle of the Bulge in World War II. It took place in December 1944 and was fought between American and German forces for control of the town of Foy in Belgium. The battle resulted in a decisive victory for the Allied forces and contributed to the eventual defeat of the German offensive.',
                'points' => ['West Bend', 'Southern Edge', 'Dugout Barn'],
            ],
            [
                'alias' => 'elsenborn',
                'name' => 'Elsenborn Ridge',
                'timeline' => '1944 (diciembre)',
                'location' => 'Western Front. Elsenborn Ridge, Belgium',
                'description' => 'The Battle of Elsenborn Ridge was a significant engagement during the Battle of the Bulge in World War II. It took place in December 1944 and was fought between American and German forces for control of the Elsenborn Ridge in Belgium. The battle resulted in a decisive victory for the Allied forces and contributed to the eventual defeat of the German offensive.',
                'points' => ['Road to Elsenborn Ridge', 'Dug Out Tank', 'Checkpoint'],
            ],
            [
                'alias' => 'remagen',
                'name' => 'Remagen',
                'timeline' => '1945 (marzo)',
                'location' => 'Western Front. Remagen, Germany',
                'description' => 'The Battle of Remagen was a significant engagement during the final stages of World War II. It took place in March 1945 and was fought between American and German forces for control of the Ludendorff Bridge at Remagen, Germany. The battle resulted in a decisive victory for the Allied forces, allowing them to establish a bridgehead across the Rhine River and hastening the end of the war in Europe.',
                'points' => ['St Severin Chapel', 'Ludendorf Bridge', 'Bauernhof Am Rhein'],
            ],
        ];

        foreach ($maps as $mapData) {
            $map = Map::query()->updateOrCreate([
                'alias' => $mapData['alias'],
            ], [
                'name' => $mapData['name'],
                'timeline' => $mapData['timeline'],
                'location' => $mapData['location'],
                'description' => $mapData['description'],
            ]);

            foreach ($mapData['points'] as $index => $point) {
                $map->centralPoints()->updateOrCreate(
                    ['order' => $index + 1],
                    ['name' => $point]
                );
            }
        }
    }
}
