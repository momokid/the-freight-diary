<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HSCodeSeeder extends Seeder
{
    public function run(): void
    {
        // ── Ghana ECOWAS CET duty bands ──────────────────────────────────────
        // Band 0 = 0%   Essential social goods (medicines, books, basic food)
        // Band 1 = 5%   Raw materials, capital goods, essential commodities
        // Band 2 = 10%  Intermediate goods
        // Band 3 = 20%  Consumer goods (most imports)
        // Band 4 = 35%  Sensitive/protected goods

        $codes = [

            // ════════════════════════════════════════════════════════════════
            // SECTION I — LIVE ANIMALS & ANIMAL PRODUCTS (Ch 01–05)
            // ════════════════════════════════════════════════════════════════

            ['01', 'Live animals', '0101', 'Live horses, asses, mules and hinnies', '0101', 5.00, '1',
                'Live equine animals for breeding or other purposes',
                'horses, donkeys, mules, breeding animals, equine',
                'Excludes: circus or zoo animals (Chapter 95)'],

            ['01', 'Live animals', '0102', 'Live bovine animals', '0102', 5.00, '1',
                'Live cattle and buffalo',
                'cattle, cows, bulls, buffalo, bovine, livestock',
                'Excludes: pure-bred breeding animals may attract reduced duty'],

            ['01', 'Live animals', '0103', 'Live swine', '0103', 20.00, '3',
                'Live pigs and hogs',
                'pigs, swine, hogs, boars, sows',
                null],

            ['01', 'Live animals', '0104', 'Live sheep and goats', '0104', 5.00, '1',
                'Live sheep and goats for farming or breeding',
                'sheep, goats, lambs, livestock, farming animals',
                null],

            ['01', 'Live animals', '0105', 'Live poultry', '0105', 20.00, '3',
                'Live fowls, ducks, geese, turkeys, guinea fowls',
                'chickens, poultry, fowl, ducks, turkeys, geese, birds',
                null],

            ['01', 'Live animals', '0106', 'Other live animals', '0106', 20.00, '3',
                'Other live animals including mammals, reptiles, birds, insects',
                'animals, pets, reptiles, insects, live animals, zoo animals',
                null],

            ['02', 'Meat and edible meat offal', '0201', 'Meat of bovine animals, fresh or chilled', '0201', 20.00, '3',
                'Fresh or chilled beef and veal',
                'beef, fresh meat, chilled meat, veal, bovine meat',
                'Excludes: frozen meat (0202)'],

            ['02', 'Meat and edible meat offal', '0202', 'Meat of bovine animals, frozen', '0202', 20.00, '3',
                'Frozen beef and veal',
                'frozen beef, frozen meat, beef, veal',
                null],

            ['02', 'Meat and edible meat offal', '0203', 'Meat of swine, fresh/chilled/frozen', '0203', 20.00, '3',
                'Pork — fresh, chilled or frozen',
                'pork, pig meat, ham, bacon, fresh pork, frozen pork',
                null],

            ['02', 'Meat and edible meat offal', '0207', 'Meat and edible offal of poultry', '0207', 20.00, '3',
                'Chicken, turkey, duck, goose meat',
                'chicken, poultry, turkey, broiler, frozen chicken, whole chicken',
                null],

            ['03', 'Fish and crustaceans', '0301', 'Live fish', '0301', 20.00, '3',
                'Live fish for food, ornamental or farming purposes',
                'live fish, aquarium fish, fish farming',
                null],

            ['03', 'Fish and crustaceans', '0302', 'Fish, fresh or chilled', '0302', 20.00, '3',
                'Fresh or chilled fish, excluding fillets',
                'fresh fish, chilled fish, whole fish, tilapia, tuna, sardines',
                null],

            ['03', 'Fish and crustaceans', '0303', 'Fish, frozen', '0303', 20.00, '3',
                'Frozen fish excluding fillets',
                'frozen fish, frozen tuna, frozen salmon, mackerel, herring',
                null],

            ['03', 'Fish and crustaceans', '0304', 'Fish fillets', '0304', 20.00, '3',
                'Fish fillets and other fish meat — fresh, chilled or frozen',
                'fish fillet, fish meat, frozen fillet, salmon fillet',
                null],

            ['03', 'Fish and crustaceans', '0306', 'Crustaceans', '0306', 20.00, '3',
                'Shrimps, prawns, lobsters, crabs',
                'shrimp, prawns, lobster, crab, crustaceans, seafood',
                null],

            ['04', 'Dairy, eggs, honey', '0401', 'Milk and cream, not concentrated', '0401', 20.00, '3',
                'Fresh milk and cream',
                'milk, fresh milk, cream, dairy, full cream milk',
                null],

            ['04', 'Dairy, eggs, honey', '0402', 'Milk and cream, concentrated', '0402', 20.00, '3',
                'Powdered milk, condensed milk, evaporated milk',
                'powdered milk, milk powder, condensed milk, evaporated milk, dairy',
                null],

            ['04', 'Dairy, eggs, honey', '0406', 'Cheese and curd', '0406', 20.00, '3',
                'All types of cheese',
                'cheese, cheddar, mozzarella, processed cheese, dairy',
                null],

            ['04', 'Dairy, eggs, honey', '0407', 'Birds eggs, in shell', '0407', 20.00, '3',
                'Eggs in shell — fresh, preserved or cooked',
                'eggs, chicken eggs, hatching eggs, fresh eggs',
                null],

            // ════════════════════════════════════════════════════════════════
            // SECTION II — VEGETABLE PRODUCTS (Ch 06–14)
            // ════════════════════════════════════════════════════════════════

            ['06', 'Live plants', '0601', 'Bulbs, tubers, roots', '0601', 10.00, '2',
                'Bulbs, tubers, tuberous roots for planting',
                'bulbs, plant roots, tubers, seedlings, planting material',
                null],

            ['06', 'Live plants', '0602', 'Other live plants', '0602', 20.00, '3',
                'Shrubs, trees, plants for landscaping or decoration',
                'plants, flowers, trees, shrubs, seedlings, nursery, ornamental',
                null],

            ['07', 'Vegetables', '0701', 'Potatoes', '0701', 20.00, '3',
                'Fresh or chilled potatoes',
                'potatoes, fresh potatoes, irish potatoes',
                null],

            ['07', 'Vegetables', '0702', 'Tomatoes', '0702', 20.00, '3',
                'Fresh or chilled tomatoes',
                'tomatoes, fresh tomatoes, cherry tomatoes',
                null],

            ['07', 'Vegetables', '0703', 'Onions, shallots, garlic, leeks', '0703', 20.00, '3',
                'Fresh or chilled alliums',
                'onions, garlic, shallots, leeks, vegetables',
                null],

            ['07', 'Vegetables', '0710', 'Vegetables, frozen', '0710', 20.00, '3',
                'Frozen vegetables of all types',
                'frozen vegetables, frozen peas, frozen carrots, mixed vegetables',
                null],

            ['08', 'Fruit and nuts', '0801', 'Coconuts, brazil nuts, cashew nuts', '0801', 20.00, '3',
                'Fresh or dried coconuts, brazil nuts and cashew nuts',
                'coconuts, cashew nuts, brazil nuts, nuts',
                null],

            ['08', 'Fruit and nuts', '0803', 'Bananas and plantains', '0803', 20.00, '3',
                'Fresh or dried bananas and plantains',
                'bananas, plantains, fresh bananas',
                null],

            ['08', 'Fruit and nuts', '0805', 'Citrus fruit', '0805', 20.00, '3',
                'Oranges, lemons, limes, grapefruit',
                'oranges, lemons, limes, grapefruit, citrus, fresh fruit',
                null],

            ['08', 'Fruit and nuts', '0806', 'Grapes', '0806', 20.00, '3',
                'Fresh or dried grapes',
                'grapes, fresh grapes, raisins, dried grapes',
                null],

            ['09', 'Coffee, tea, spices', '0901', 'Coffee', '0901', 20.00, '3',
                'Coffee, whether or not roasted or decaffeinated',
                'coffee, roasted coffee, coffee beans, instant coffee, ground coffee',
                null],

            ['09', 'Coffee, tea, spices', '0902', 'Tea', '0902', 20.00, '3',
                'Tea, whether or not flavoured',
                'tea, green tea, black tea, herbal tea, tea bags',
                null],

            ['09', 'Coffee, tea, spices', '0904', 'Pepper', '0904', 20.00, '3',
                'Pepper of the genus Piper; dried or crushed capsicum',
                'pepper, black pepper, white pepper, chilli pepper, spices',
                null],

            ['10', 'Cereals', '1001', 'Wheat and meslin', '1001', 5.00, '1',
                'Wheat and meslin grain — essential commodity',
                'wheat, meslin, wheat grain, hard wheat, soft wheat',
                null],

            ['10', 'Cereals', '1005', 'Maize (corn)', '1005', 5.00, '1',
                'Maize grain',
                'maize, corn, grain, maize grain',
                null],

            ['10', 'Cereals', '1006', 'Rice', '1006', 10.00, '2',
                'Rice in all forms — husked, milled, broken',
                'rice, white rice, brown rice, parboiled rice, milled rice, broken rice',
                null],

            ['10', 'Cereals', '1007', 'Grain sorghum', '1007', 5.00, '1',
                'Sorghum grain',
                'sorghum, grain sorghum, milo',
                null],

            ['11', 'Milling products', '1101', 'Wheat or meslin flour', '1101', 20.00, '3',
                'Flour of wheat or meslin',
                'wheat flour, flour, meslin flour, baking flour, bread flour',
                null],

            ['11', 'Milling products', '1102', 'Cereal flours other than wheat', '1102', 20.00, '3',
                'Maize flour, rice flour, other cereal flours',
                'maize flour, corn flour, rice flour, cereal flour',
                null],

            // ════════════════════════════════════════════════════════════════
            // SECTION IV — PREPARED FOODS, BEVERAGES (Ch 16–24)
            // ════════════════════════════════════════════════════════════════

            ['16', 'Prepared meat/fish', '1601', 'Sausages and similar products', '1601', 20.00, '3',
                'Sausages, hot dogs, salami and similar products',
                'sausages, hot dogs, salami, processed meat, luncheon meat',
                null],

            ['16', 'Prepared meat/fish', '1604', 'Prepared or preserved fish', '1604', 20.00, '3',
                'Canned fish, sardines, tuna, mackerel in cans',
                'canned fish, sardines, tuna cans, mackerel, preserved fish, fish tins',
                null],

            ['17', 'Sugars', '1701', 'Cane or beet sugar, solid', '1701', 20.00, '3',
                'Raw and refined sugar',
                'sugar, cane sugar, refined sugar, white sugar, raw sugar',
                null],

            ['17', 'Sugars', '1704', 'Sugar confectionery', '1704', 20.00, '3',
                'Sweets, chewing gum, toffees, chocolates without cocoa',
                'sweets, candy, confectionery, toffee, chewing gum, lollipop',
                null],

            ['18', 'Cocoa', '1801', 'Cocoa beans', '1801', 0.00, '0',
                'Raw cocoa beans — Ghana export product',
                'cocoa beans, raw cocoa, cacao',
                null],

            ['18', 'Cocoa', '1806', 'Chocolate and cocoa food preparations', '1806', 20.00, '3',
                'Chocolate bars, cocoa powder, chocolate beverages',
                'chocolate, cocoa powder, chocolate bar, candy, confectionery',
                null],

            ['19', 'Preparations of cereals', '1901', 'Malt extract, cereal preparations', '1901', 20.00, '3',
                'Malt extract, cereal-based infant food, mixes and doughs',
                'malt, malt extract, infant cereal, cereal preparation, baking mix',
                null],

            ['19', 'Preparations of cereals', '1902', 'Pasta', '1902', 20.00, '3',
                'Spaghetti, macaroni, noodles, lasagna',
                'pasta, spaghetti, noodles, macaroni, lasagna, vermicelli',
                null],

            ['19', 'Preparations of cereals', '1904', 'Cereals in grain form', '1904', 20.00, '3',
                'Corn flakes, muesli, puffed rice, breakfast cereals',
                'cornflakes, breakfast cereal, muesli, granola, oats, porridge',
                null],

            ['19', 'Preparations of cereals', '1905', 'Bread, pastry, biscuits', '1905', 20.00, '3',
                'Baked goods — bread, cakes, biscuits, crackers, wafers',
                'biscuits, bread, crackers, wafers, cakes, pastry, cookies',
                null],

            ['20', 'Preparations of vegetables', '2001', 'Vegetables preserved in vinegar', '2001', 20.00, '3',
                'Pickled vegetables, pickles, gherkins, olives',
                'pickles, gherkins, olives, pickled vegetables, vinegar preserved',
                null],

            ['20', 'Preparations of vegetables', '2002', 'Tomatoes prepared or preserved', '2002', 20.00, '3',
                'Canned tomatoes, tomato paste, tomato puree',
                'tomato paste, tomato puree, canned tomatoes, tomato sauce, ketchup base',
                null],

            ['20', 'Preparations of vegetables', '2009', 'Fruit juices', '2009', 20.00, '3',
                'Unfermented fruit and vegetable juices',
                'fruit juice, orange juice, apple juice, pineapple juice, mango juice',
                null],

            ['21', 'Miscellaneous food', '2101', 'Extracts of coffee/tea', '2101', 20.00, '3',
                'Instant coffee, tea extracts, chicory extracts',
                'instant coffee, coffee extract, nescafe, tea extract, milo',
                null],

            ['21', 'Miscellaneous food', '2103', 'Sauces, mixed condiments', '2103', 20.00, '3',
                'Soy sauce, tomato ketchup, mustard, mixed seasonings',
                'soy sauce, ketchup, mustard, condiments, seasoning, sauce, spice mix',
                null],

            ['21', 'Miscellaneous food', '2106', 'Food preparations NEC', '2106', 20.00, '3',
                'Protein concentrates, food supplements, flavoured syrups',
                'food supplement, protein powder, food preparation, flavouring, syrup',
                null],

            ['22', 'Beverages', '2201', 'Waters, natural/mineral', '2201', 20.00, '3',
                'Natural mineral water, spring water, ice',
                'water, mineral water, spring water, bottled water, ice',
                null],

            ['22', 'Beverages', '2202', 'Waters with added sugar', '2202', 20.00, '3',
                'Soft drinks, energy drinks, flavoured water',
                'soft drinks, fizzy drinks, energy drinks, soda, cola, fanta, sprite',
                null],

            ['22', 'Beverages', '2203', 'Beer made from malt', '2203', 20.00, '3',
                'Beer, ale, stout, lager',
                'beer, lager, stout, ale, malt drink',
                null],

            ['22', 'Beverages', '2204', 'Wine of fresh grapes', '2204', 20.00, '3',
                'Wine — still and sparkling',
                'wine, red wine, white wine, sparkling wine, champagne',
                null],

            ['22', 'Beverages', '2208', 'Spirits — whisky, brandy, vodka', '2208', 20.00, '3',
                'Distilled spirits — whisky, brandy, gin, vodka, rum',
                'whisky, vodka, gin, rum, brandy, spirits, liquor, schnapps',
                null],

            ['24', 'Tobacco', '2401', 'Unmanufactured tobacco', '2401', 20.00, '3',
                'Raw tobacco leaf',
                'tobacco leaf, raw tobacco, cured tobacco',
                null],

            ['24', 'Tobacco', '2402', 'Cigars, cigarettes', '2402', 20.00, '3',
                'Cigars, cigarettes and cigarillos',
                'cigarettes, cigars, tobacco products, cigarette packs',
                null],

            // ════════════════════════════════════════════════════════════════
            // SECTION V — MINERAL PRODUCTS (Ch 25–27)
            // ════════════════════════════════════════════════════════════════

            ['25', 'Salt, sulphur, earth, stone', '2501', 'Salt', '2501', 0.00, '0',
                'All forms of salt including table salt and industrial salt',
                'salt, table salt, rock salt, sea salt, sodium chloride',
                null],

            ['25', 'Salt, sulphur, earth, stone', '2505', 'Natural sands', '2505', 5.00, '1',
                'Natural sands of all kinds',
                'sand, building sand, silica sand, river sand',
                null],

            ['25', 'Salt, sulphur, earth, stone', '2515', 'Marble, granite', '2515', 10.00, '2',
                'Marble, travertine, granite for construction',
                'marble, granite, stone, tiles, building stone, travertine',
                null],

            ['25', 'Salt, sulphur, earth, stone', '2523', 'Portland cement', '2523', 10.00, '2',
                'Portland cement, aluminous cement, clinkers',
                'cement, portland cement, building materials, construction, concrete',
                null],

            ['26', 'Ores, slag and ash', '2601', 'Iron ores and concentrates', '2601', 5.00, '1',
                'Iron ore, iron concentrates, roasted iron pyrites',
                'iron ore, ore, mining, concentrate',
                null],

            ['26', 'Ores, slag and ash', '2614', 'Titanium ores', '2614', 5.00, '1',
                'Titanium ores and concentrates',
                'titanium ore, mineral ore',
                null],

            ['27', 'Mineral fuels', '2701', 'Coal', '2701', 5.00, '1',
                'Coal, briquettes, ovoids and similar solid fuels',
                'coal, charcoal, solid fuel, coke, briquettes',
                null],

            ['27', 'Mineral fuels', '2710', 'Petroleum oils', '2710', 5.00, '1',
                'Motor spirit, gas oil, fuel oil, lubricating oil',
                'petrol, diesel, fuel oil, petroleum, lubricant, engine oil, kerosene',
                'Excludes: crude petroleum (2709)'],

            ['27', 'Mineral fuels', '2711', 'Petroleum gases', '2711', 5.00, '1',
                'LPG, natural gas, propane, butane',
                'LPG, gas, propane, butane, natural gas, cooking gas, cylinder gas',
                null],

            // ════════════════════════════════════════════════════════════════
            // SECTION VI — CHEMICALS (Ch 28–38)
            // ════════════════════════════════════════════════════════════════

            ['28', 'Inorganic chemicals', '2804', 'Hydrogen, oxygen, noble gases', '2804', 5.00, '1',
                'Industrial gases — oxygen, nitrogen, argon, hydrogen',
                'oxygen, nitrogen, industrial gas, hydrogen, argon, welding gas',
                null],

            ['28', 'Inorganic chemicals', '2814', 'Ammonia', '2814', 5.00, '1',
                'Anhydrous ammonia and ammonia in aqueous solution',
                'ammonia, fertilizer, chemical',
                null],

            ['29', 'Organic chemicals', '2901', 'Acyclic hydrocarbons', '2901', 5.00, '1',
                'Ethylene, propylene, butylene and similar',
                'ethylene, propylene, chemical, hydrocarbon',
                null],

            ['30', 'Pharmaceutical products', '3001', 'Human/animal blood', '3001', 0.00, '0',
                'Glands, organs, blood for therapeutic uses',
                'blood, plasma, serum, medical, pharmaceutical',
                null],

            ['30', 'Pharmaceutical products', '3002', 'Vaccines, blood products', '3002', 0.00, '0',
                'Vaccines, toxins, cultures of micro-organisms',
                'vaccine, blood, pharmaceutical, medicine, antitoxin',
                null],

            ['30', 'Pharmaceutical products', '3003', 'Medicaments (mixed)', '3003', 0.00, '0',
                'Medicines consisting of two or more mixed ingredients',
                'medicine, drugs, pharmaceutical, tablets, capsules, medication',
                null],

            ['30', 'Pharmaceutical products', '3004', 'Medicaments (retail packs)', '3004', 0.00, '0',
                'Packaged medicines for retail sale',
                'medicine, drugs, tablets, capsules, syrup, pharmaceutical, antibiotics, paracetamol',
                'Includes most over-the-counter and prescription drugs'],

            ['30', 'Pharmaceutical products', '3005', 'Wadding, bandages, dressings', '3005', 0.00, '0',
                'Medical wadding, gauze, bandages, surgical dressings',
                'bandage, dressing, wadding, gauze, medical supplies, first aid',
                null],

            ['31', 'Fertilisers', '3101', 'Animal or vegetable fertilisers', '3101', 5.00, '1',
                'Organic fertilisers, manure, compost',
                'fertilizer, compost, manure, organic fertilizer, npk',
                null],

            ['31', 'Fertilisers', '3102', 'Mineral/chemical nitrogen fertilisers', '3102', 5.00, '1',
                'Urea, ammonium nitrate, ammonium sulphate',
                'urea, fertilizer, nitrogen, ammonium nitrate, agricultural input',
                null],

            ['31', 'Fertilisers', '3105', 'Mixed mineral fertilisers', '3105', 5.00, '1',
                'NPK compound fertilisers',
                'NPK fertilizer, compound fertilizer, mixed fertilizer',
                null],

            ['32', 'Tanning, dyeing, paints', '3208', 'Paints and varnishes — synthetic', '3208', 20.00, '3',
                'Paints, varnishes, lacquers based on synthetic polymers',
                'paint, varnish, lacquer, coating, wall paint, industrial paint',
                null],

            ['32', 'Tanning, dyeing, paints', '3209', 'Paints and varnishes — aqueous', '3209', 20.00, '3',
                'Paints and varnishes based on acrylic, vinyl polymers in water',
                'emulsion paint, water based paint, acrylic paint, latex paint',
                null],

            ['32', 'Tanning, dyeing, paints', '3212', 'Pigments, inks', '3212', 20.00, '3',
                'Printing inks, stamping foils, dyes, pigments',
                'printing ink, ink, pigment, dye, toner',
                null],

            ['33', 'Essential oils, cosmetics', '3301', 'Essential oils', '3301', 20.00, '3',
                'Essential oils of citrus fruit, other plants',
                'essential oil, fragrance oil, perfume oil, aromatic',
                null],

            ['33', 'Essential oils, cosmetics', '3303', 'Perfumes and toilet waters', '3303', 20.00, '3',
                'Perfumes, colognes, eau de toilette',
                'perfume, cologne, fragrance, eau de toilette, aftershave',
                null],

            ['33', 'Essential oils, cosmetics', '3304', 'Beauty/make-up preparations', '3304', 20.00, '3',
                'Lipstick, eye make-up, face powder, skin care creams',
                'lipstick, makeup, cosmetics, foundation, cream, lotion, skincare, face cream, moisturizer',
                null],

            ['33', 'Essential oils, cosmetics', '3305', 'Hair preparations', '3305', 20.00, '3',
                'Shampoo, hair conditioner, hair oil, styling products',
                'shampoo, conditioner, hair oil, hair cream, relaxer, hair product',
                null],

            ['33', 'Essential oils, cosmetics', '3306', 'Oral hygiene preparations', '3306', 20.00, '3',
                'Toothpaste, mouthwash, dental floss',
                'toothpaste, mouthwash, dental, oral care, toothbrush paste',
                null],

            ['33', 'Essential oils, cosmetics', '3307', 'Shaving/bathing preparations', '3307', 20.00, '3',
                'Shaving cream, deodorants, bath salts, room fresheners',
                'deodorant, antiperspirant, shaving cream, body spray, air freshener, bath salts',
                null],

            ['34', 'Soap, detergents', '3401', 'Soap and organic surface-active products', '3401', 20.00, '3',
                'Bar soap, liquid soap, toilet soap',
                'soap, bar soap, liquid soap, laundry soap, bath soap, detergent soap',
                null],

            ['34', 'Soap, detergents', '3402', 'Washing preparations, detergents', '3402', 20.00, '3',
                'Laundry detergents, washing powder, dishwashing liquid',
                'detergent, washing powder, laundry detergent, dishwashing liquid, cleaning product',
                null],

            ['35', 'Albuminoidal substances', '3506', 'Prepared glues', '3506', 20.00, '3',
                'Prepared glues, adhesives for retail sale',
                'glue, adhesive, bond, super glue, wood glue',
                null],

            ['38', 'Miscellaneous chemicals', '3808', 'Insecticides, herbicides, fungicides', '3808', 10.00, '2',
                'Insecticides, rodenticides, herbicides, fungicides',
                'insecticide, pesticide, herbicide, fungicide, mosquito spray, rat poison, chemicals',
                null],

            ['38', 'Miscellaneous chemicals', '3820', 'Anti-freezing preparations', '3820', 20.00, '3',
                'Hydraulic brake fluid, antifreeze',
                'antifreeze, brake fluid, hydraulic fluid, coolant',
                null],

            // ════════════════════════════════════════════════════════════════
            // SECTION VII — PLASTICS & RUBBER (Ch 39–40)
            // ════════════════════════════════════════════════════════════════

            ['39', 'Plastics', '3901', 'Polymers of ethylene', '3901', 10.00, '2',
                'Polyethylene in primary forms — raw material',
                'polyethylene, PE, plastic raw material, polymer, HDPE, LDPE',
                null],

            ['39', 'Plastics', '3917', 'Tubes, pipes of plastics', '3917', 20.00, '3',
                'Plastic tubes, pipes, fittings',
                'plastic pipes, PVC pipes, plastic tubes, pipe fittings, plumbing',
                null],

            ['39', 'Plastics', '3919', 'Self-adhesive plastic plates/sheets', '3919', 20.00, '3',
                'Self-adhesive films, stickers, tape',
                'sticker, label, adhesive tape, sticky tape, plastic film',
                null],

            ['39', 'Plastics', '3923', 'Plastic containers for goods', '3923', 20.00, '3',
                'Plastic boxes, bottles, crates, bags, packaging',
                'plastic container, plastic box, plastic bag, plastic bottle, packaging, crate',
                null],

            ['39', 'Plastics', '3926', 'Other plastic articles', '3926', 20.00, '3',
                'Miscellaneous plastic articles',
                'plastic items, plastic parts, plastic goods, plastic products',
                null],

            ['40', 'Rubber', '4011', 'New pneumatic tyres of rubber', '4011', 20.00, '3',
                'New car, truck, motorcycle tyres',
                'tyres, tires, car tyres, truck tyres, motorcycle tyres, new tyres',
                'Excludes: used/retreaded tyres (4012)'],

            ['40', 'Rubber', '4012', 'Retreaded or used tyres', '4012', 20.00, '3',
                'Used or retreaded pneumatic tyres',
                'used tyres, retreaded tyres, second hand tyres',
                null],

            ['40', 'Rubber', '4016', 'Other rubber articles', '4016', 20.00, '3',
                'Rubber seals, gaskets, mats, conveyor belts',
                'rubber mat, rubber seal, gasket, rubber product, conveyor belt',
                null],

            // ════════════════════════════════════════════════════════════════
            // SECTION VIII — LEATHER, WOOD, PAPER (Ch 41–49)
            // ════════════════════════════════════════════════════════════════

            ['44', 'Wood and wood articles', '4407', 'Wood sawn or chipped', '4407', 10.00, '2',
                'Sawn timber, planks, beams',
                'timber, wood, lumber, planks, beams, sawn wood',
                null],

            ['44', 'Wood and wood articles', '4415', 'Packing cases of wood', '4415', 20.00, '3',
                'Wooden packing cases, boxes, crates, pallets',
                'wooden pallet, wooden crate, wood box, packing, wooden container',
                null],

            ['44', 'Wood and wood articles', '4418', 'Builders joinery of wood', '4418', 20.00, '3',
                'Doors, windows, shutters, staircases of wood',
                'wooden door, wood window, wooden staircase, joinery',
                null],

            ['44', 'Wood and wood articles', '4421', 'Other wood articles', '4421', 20.00, '3',
                'Clothes hangers, wood tools, wooden matches, toothpicks',
                'wooden items, wood products, matches, toothpicks, hangers',
                null],

            ['48', 'Paper and paperboard', '4802', 'Uncoated paper for writing', '4802', 10.00, '2',
                'Printing paper, writing paper, offset paper',
                'paper, printing paper, A4 paper, writing paper, office paper',
                null],

            ['48', 'Paper and paperboard', '4819', 'Cartons, boxes of paper', '4819', 20.00, '3',
                'Cardboard boxes, paper cartons, packaging',
                'cardboard box, carton, paper packaging, corrugated box',
                null],

            ['48', 'Paper and paperboard', '4820', 'Registers, exercise books', '4820', 0.00, '0',
                'Exercise books, notebooks, diaries, ledger books',
                'exercise book, notebook, diary, register, stationery, school books',
                null],

            ['49', 'Printed books', '4901', 'Printed books', '4901', 0.00, '0',
                'Printed books, brochures, pamphlets',
                'books, printed books, textbooks, novels, educational books, brochures',
                null],

            ['49', 'Printed books', '4902', 'Newspapers and periodicals', '4902', 0.00, '0',
                'Newspapers, journals, magazines',
                'newspaper, magazine, journal, periodical, newsletter',
                null],

            // ════════════════════════════════════════════════════════════════
            // SECTION XI — TEXTILES (Ch 50–63)
            // ════════════════════════════════════════════════════════════════

            ['52', 'Cotton', '5201', 'Cotton, not carded or combed', '5201', 5.00, '1',
                'Raw cotton — agricultural raw material',
                'cotton, raw cotton, cotton bale, fibre',
                null],

            ['52', 'Cotton', '5208', 'Woven fabrics of cotton', '5208', 20.00, '3',
                'Woven cotton fabrics — shirting, sheeting, canvas',
                'cotton fabric, woven fabric, cotton cloth, shirting, canvas',
                null],

            ['61', 'Knitted clothing', '6101', 'Mens overcoats of knit', '6101', 20.00, '3',
                'Mens knitted overcoats, cloaks, anoraks, windcheaters',
                'mens jacket, overcoat, anorak, windcheater, mens clothing',
                null],

            ['61', 'Knitted clothing', '6105', 'Mens shirts of knit', '6105', 20.00, '3',
                'Mens T-shirts, polo shirts',
                'mens shirt, t-shirt, polo shirt, mens clothing, jersey',
                null],

            ['61', 'Knitted clothing', '6109', 'T-shirts and vests of knit', '6109', 20.00, '3',
                'T-shirts, singlets, vests',
                't-shirt, vest, singlet, top, clothing',
                null],

            ['61', 'Knitted clothing', '6110', 'Jerseys, pullovers of knit', '6110', 20.00, '3',
                'Jerseys, pullovers, sweatshirts, waistcoats',
                'jersey, pullover, sweater, sweatshirt, hoodie, knitwear',
                null],

            ['62', 'Non-knit clothing', '6201', 'Mens overcoats, woven', '6201', 20.00, '3',
                'Mens overcoats, jackets, blazers — woven fabric',
                'mens jacket, blazer, suit jacket, overcoat, windbreaker',
                null],

            ['62', 'Non-knit clothing', '6203', 'Mens suits, jackets, trousers', '6203', 20.00, '3',
                'Mens suits, ensembles, jackets, trousers, jeans',
                'mens suit, trousers, jeans, pants, formal wear, mens clothing',
                null],

            ['62', 'Non-knit clothing', '6204', 'Womens suits and dresses', '6204', 20.00, '3',
                'Womens suits, dresses, skirts, trousers',
                'womens dress, suit, skirt, blouse, womens clothing, female clothing',
                null],

            ['62', 'Non-knit clothing', '6211', 'Tracksuits, swimwear', '6211', 20.00, '3',
                'Tracksuits, ski suits, swimwear',
                'tracksuit, sportswear, swimwear, gym wear, sport clothes',
                null],

            ['63', 'Made-up textiles', '6301', 'Blankets and travel rugs', '6301', 20.00, '3',
                'Blankets, travelling rugs, duvets',
                'blanket, duvet, bedding, travel rug, throw',
                null],

            ['63', 'Made-up textiles', '6302', 'Bed linen, table linen', '6302', 20.00, '3',
                'Bedsheets, pillowcases, tablecloths, towels',
                'bedsheet, pillowcase, towel, tablecloth, linen, bedding',
                null],

            ['63', 'Made-up textiles', '6303', 'Curtains and drapes', '6303', 20.00, '3',
                'Curtains, drapes, interior blinds',
                'curtain, drape, blind, window covering',
                null],

            ['63', 'Made-up textiles', '6305', 'Sacks and bags for packaging', '6305', 20.00, '3',
                'Woven sacks for commodities, pp bags, jute bags',
                'sack, bag, jute bag, pp sack, polypropylene bag, packaging bag',
                null],

            ['63', 'Made-up textiles', '6309', 'Worn clothing', '6309', 35.00, '4',
                'Used clothing — obroni wawu, second-hand clothes',
                'used clothes, second hand clothes, obroni wawu, bale clothes, mitumba',
                'High duty to protect local textile industry'],

            // ════════════════════════════════════════════════════════════════
            // SECTION XII — FOOTWEAR, HEADGEAR (Ch 64–67)
            // ════════════════════════════════════════════════════════════════

            ['64', 'Footwear', '6401', 'Waterproof footwear', '6401', 20.00, '3',
                'Boots, wellingtons with rubber/plastic outer soles',
                'boots, wellington boots, rain boots, waterproof shoes',
                null],

            ['64', 'Footwear', '6403', 'Footwear with leather uppers', '6403', 20.00, '3',
                'Shoes, boots with leather uppers',
                'leather shoes, dress shoes, boots, leather boots, formal shoes',
                null],

            ['64', 'Footwear', '6404', 'Footwear with textile uppers', '6404', 20.00, '3',
                'Sports shoes, sneakers, canvas shoes',
                'sneakers, trainers, sports shoes, canvas shoes, athletic shoes, Nike, Adidas',
                null],

            ['64', 'Footwear', '6405', 'Other footwear', '6405', 20.00, '3',
                'Sandals, slippers, other footwear',
                'sandals, slippers, flip flops, open shoes, casual shoes',
                null],

            // ════════════════════════════════════════════════════════════════
            // SECTION XIII — STONE, PLASTER, GLASS (Ch 68–70)
            // ════════════════════════════════════════════════════════════════

            ['68', 'Stone, plaster, cement', '6802', 'Worked stone for construction', '6802', 20.00, '3',
                'Worked granite, marble, slate, travertine tiles',
                'granite tiles, marble tiles, stone tiles, floor tiles, wall tiles',
                null],

            ['68', 'Stone, plaster, cement', '6810', 'Articles of cement, concrete', '6810', 20.00, '3',
                'Concrete blocks, precast concrete, tiles',
                'concrete blocks, paving blocks, precast, cement tiles',
                null],

            ['68', 'Stone, plaster, cement', '6811', 'Articles of asbestos-cement', '6811', 20.00, '3',
                'Corrugated asbestos cement sheets, roofing sheets',
                'asbestos sheet, roofing sheet, cement sheet, corrugated sheet',
                null],

            ['69', 'Ceramic products', '6907', 'Ceramic tiles', '6907', 20.00, '3',
                'Glazed ceramic tiles for walls and floors',
                'ceramic tiles, floor tiles, wall tiles, porcelain tiles, vitrified tiles',
                null],

            ['69', 'Ceramic products', '6910', 'Ceramic sinks, wash basins', '6910', 20.00, '3',
                'Ceramic sanitary ware — toilets, basins, baths',
                'toilet, wash basin, bathroom fittings, ceramic sink, sanitary ware',
                null],

            ['70', 'Glass', '7003', 'Cast/rolled glass in sheets', '7003', 10.00, '2',
                'Glass sheets, float glass, wired glass',
                'glass sheet, float glass, glass panel, window glass',
                null],

            ['70', 'Glass', '7013', 'Glassware for table/kitchen', '7013', 20.00, '3',
                'Drinking glasses, bowls, vases, cookware glass',
                'glass, drinking glass, bowl, glassware, vase, Pyrex',
                null],

            // ════════════════════════════════════════════════════════════════
            // SECTION XV — BASE METALS (Ch 72–83)
            // ════════════════════════════════════════════════════════════════

            ['72', 'Iron and steel', '7208', 'Flat-rolled steel, hot-rolled', '7208', 10.00, '2',
                'Hot-rolled coils, sheets and strips of steel',
                'steel sheet, steel coil, hot rolled steel, iron sheet, steel plate',
                null],

            ['72', 'Iron and steel', '7213', 'Steel bars, rods in coils', '7213', 10.00, '2',
                'Hot-rolled steel rods in irregularly wound coils',
                'steel rod, rebar, reinforcement bar, rod coil',
                null],

            ['72', 'Iron and steel', '7214', 'Steel bars and rods', '7214', 10.00, '2',
                'Steel reinforcement bars — straight rods',
                'rebar, reinforcement bar, steel bar, iron bar, construction steel',
                null],

            ['72', 'Iron and steel', '7217', 'Wire of iron or non-alloy steel', '7217', 20.00, '3',
                'Steel wire, wire rods',
                'steel wire, wire rod, iron wire, binding wire, fence wire',
                null],

            ['72', 'Iron and steel', '7219', 'Flat-rolled stainless steel', '7219', 10.00, '2',
                'Stainless steel sheets, coils',
                'stainless steel, stainless sheet, stainless coil',
                null],

            ['72', 'Iron and steel', '7228', 'Other bars and rods of alloy steel', '7228', 10.00, '2',
                'Alloy steel bars, angles, shapes',
                'alloy steel, steel bar, structural steel, angle bar, I-beam',
                null],

            ['73', 'Iron/steel articles', '7301', 'Sheet piling, welded angles', '7301', 10.00, '2',
                'Sheet piling, angles, shapes of iron/steel',
                'sheet pile, angle iron, steel angle, structural steel',
                null],

            ['73', 'Iron/steel articles', '7302', 'Railway track construction', '7302', 5.00, '1',
                'Rails, sleepers, fishplates for railways',
                'railway track, rails, sleepers, rail line',
                null],

            ['73', 'Iron/steel articles', '7303', 'Tubes and pipes of cast iron', '7303', 20.00, '3',
                'Cast iron pipes and tubes',
                'cast iron pipe, drainage pipe, manhole cover',
                null],

            ['73', 'Iron/steel articles', '7304', 'Tubes and pipes of steel (seamless)', '7304', 10.00, '2',
                'Seamless steel tubes and pipes',
                'steel pipe, seamless pipe, steel tube, hollow section',
                null],

            ['73', 'Iron/steel articles', '7306', 'Tubes and pipes of steel (welded)', '7306', 10.00, '2',
                'Welded steel pipes and tubes',
                'welded pipe, steel pipe, ERW pipe, water pipe, gas pipe',
                null],

            ['73', 'Iron/steel articles', '7308', 'Structures of iron or steel', '7308', 10.00, '2',
                'Bridges, towers, columns, steel structures',
                'steel structure, steel frame, bridge, tower, column, steel construction',
                null],

            ['73', 'Iron/steel articles', '7309', 'Reservoirs, tanks of iron/steel', '7309', 20.00, '3',
                'Storage tanks, containers of iron/steel',
                'storage tank, steel tank, water tank, fuel tank, container',
                null],

            ['73', 'Iron/steel articles', '7312', 'Steel wire, rope, cables', '7312', 20.00, '3',
                'Stranded wire, ropes, cables, plaited bands',
                'wire rope, steel cable, stranded wire, lifting cable',
                null],

            ['73', 'Iron/steel articles', '7315', 'Chain and parts of iron/steel', '7315', 20.00, '3',
                'Chains — roller, link, articulated',
                'chain, link chain, roller chain, steel chain',
                null],

            ['73', 'Iron/steel articles', '7317', 'Nails, tacks, staples', '7317', 20.00, '3',
                'Nails, tacks, drawing pins, corrugated nails',
                'nails, tacks, staples, roofing nails, wire nails',
                null],

            ['73', 'Iron/steel articles', '7318', 'Screws, bolts, nuts, washers', '7318', 20.00, '3',
                'Screws, bolts, nuts, washers, rivets of iron/steel',
                'bolts, nuts, screws, washers, fasteners, rivets, hardware',
                null],

            ['73', 'Iron/steel articles', '7323', 'Table/kitchen/domestic articles', '7323', 20.00, '3',
                'Pots, pans, buckets, bowls of iron/steel',
                'pots, pans, cooking pots, buckets, bowls, kitchen utensils, steel cookware',
                null],

            ['74', 'Copper', '7401', 'Copper mattes', '7401', 5.00, '1',
                'Copper mattes and cement copper',
                'copper ore, copper matte, raw copper',
                null],

            ['74', 'Copper', '7408', 'Copper wire', '7408', 10.00, '2',
                'Copper wire for electrical use',
                'copper wire, electrical wire, copper cable, winding wire',
                null],

            ['76', 'Aluminium', '7601', 'Unwrought aluminium', '7601', 5.00, '1',
                'Aluminium ingots, billets, slabs',
                'aluminium ingot, billet, raw aluminium',
                null],

            ['76', 'Aluminium', '7606', 'Aluminium plates, sheets, strip', '7606', 10.00, '2',
                'Aluminium sheets and coils',
                'aluminium sheet, aluminium coil, aluminium plate, aluminium roofing',
                null],

            ['76', 'Aluminium', '7610', 'Aluminium structures', '7610', 20.00, '3',
                'Aluminium doors, windows, structures',
                'aluminium door, aluminium window, aluminium frame, aluminium structure',
                null],

            ['76', 'Aluminium', '7612', 'Aluminium casks and drums', '7612', 20.00, '3',
                'Aluminium containers, cans, drums',
                'aluminium drum, aluminium can, aluminium container',
                null],

            ['82', 'Tools, implements', '8201', 'Hand tools — spades, forks', '8201', 20.00, '3',
                'Spades, shovels, mattocks, picks, hoes, forks',
                'spade, shovel, hoe, fork, mattock, garden tools, hand tools',
                null],

            ['82', 'Tools, implements', '8205', 'Hand tools NEC', '8205', 20.00, '3',
                'Hammers, screwdrivers, spanners, pliers',
                'hammer, screwdriver, spanner, wrench, pliers, hand tools, tool kit',
                null],

            ['82', 'Tools, implements', '8208', 'Knives and cutting blades', '8208', 20.00, '3',
                'Knives for machines, kitchen, other purposes',
                'knife, blade, cutting blade, kitchen knife, cutter',
                null],

            ['82', 'Tools, implements', '8211', 'Knives with cutting blades', '8211', 20.00, '3',
                'Knives, penknives, table knives',
                'knife, penknife, table knife, cutlery',
                null],

            // ════════════════════════════════════════════════════════════════
            // SECTION XVI — MACHINERY & ELECTRICAL (Ch 84–85)
            // ════════════════════════════════════════════════════════════════

            ['84', 'Machinery', '8401', 'Nuclear reactors', '8401', 5.00, '1',
                'Nuclear reactors, fuel elements, isotope separators',
                'nuclear reactor, fuel element, nuclear equipment',
                null],

            ['84', 'Machinery', '8408', 'Compression ignition engines (diesel)', '8408', 10.00, '2',
                'Diesel engines for industrial or vehicle use',
                'diesel engine, compression engine, industrial engine',
                null],

            ['84', 'Machinery', '8413', 'Pumps for liquids', '8413', 10.00, '2',
                'Water pumps, fuel pumps, hydraulic pumps',
                'pump, water pump, fuel pump, hydraulic pump, centrifugal pump',
                null],

            ['84', 'Machinery', '8415', 'Air conditioning machines', '8415', 20.00, '3',
                'Air conditioners — window, split, central',
                'air conditioner, AC, air conditioning, split AC, window unit',
                null],

            ['84', 'Machinery', '8418', 'Refrigerators and freezers', '8418', 20.00, '3',
                'Household and commercial refrigerators and freezers',
                'refrigerator, fridge, freezer, cold room, chest freezer, ice machine',
                null],

            ['84', 'Machinery', '8419', 'Industrial heaters, cookers, dryers', '8419', 10.00, '2',
                'Industrial heating, cooling, drying equipment',
                'industrial dryer, heater, heat exchanger, industrial equipment',
                null],

            ['84', 'Machinery', '8421', 'Centrifuges, filters, purifiers', '8421', 10.00, '2',
                'Centrifuges, filtering machinery, water purifiers',
                'centrifuge, filter, water purifier, filtration, separator',
                null],

            ['84', 'Machinery', '8422', 'Dishwashers, packaging machinery', '8422', 10.00, '2',
                'Dishwashers, bottle filling, sealing, labelling machines',
                'dishwasher, packaging machine, sealing machine, filling machine',
                null],

            ['84', 'Machinery', '8424', 'Spraying machines', '8424', 10.00, '2',
                'Agricultural sprayers, fire extinguishers, spray guns',
                'sprayer, agricultural sprayer, spray gun, fire extinguisher',
                null],

            ['84', 'Machinery', '8425', 'Pulleys, hoists, winches, jacks', '8425', 10.00, '2',
                'Lifting equipment — hoists, winches, jacks',
                'hoist, winch, jack, pulley, lifting equipment, crane',
                null],

            ['84', 'Machinery', '8426', 'Ships derricks, cranes', '8426', 5.00, '1',
                'Overhead cranes, portal cranes, mobile cranes',
                'crane, derrick, overhead crane, mobile crane, gantry crane',
                null],

            ['84', 'Machinery', '8427', 'Fork-lift trucks', '8427', 10.00, '2',
                'Fork-lift trucks and works trucks',
                'forklift, fork-lift truck, works truck, warehouse truck',
                null],

            ['84', 'Machinery', '8429', 'Bulldozers, graders, excavators', '8429', 5.00, '1',
                'Self-propelled earthmoving equipment',
                'bulldozer, grader, excavator, crawler, earthmover, construction equipment, caterpillar',
                null],

            ['84', 'Machinery', '8430', 'Other earthmoving machinery', '8430', 5.00, '1',
                'Boring machines, pile drivers, tunnelling machinery',
                'boring machine, pile driver, drilling rig, tunnelling machine',
                null],

            ['84', 'Machinery', '8431', 'Parts for lifting/earthmoving machinery', '8431', 10.00, '2',
                'Parts and accessories for machines of 8425-8430',
                'crane parts, excavator parts, bulldozer parts, machinery parts',
                null],

            ['84', 'Machinery', '8432', 'Agricultural machinery for soil', '8432', 5.00, '1',
                'Ploughs, harrows, cultivators, seeders, spreaders',
                'plough, tractor, agricultural equipment, seeder, harrow, cultivator',
                null],

            ['84', 'Machinery', '8433', 'Harvesting machinery', '8433', 5.00, '1',
                'Combine harvesters, threshing machines, lawn mowers',
                'combine harvester, harvester, thresher, mower, grass cutter',
                null],

            ['84', 'Machinery', '8436', 'Other agricultural machinery', '8436', 5.00, '1',
                'Poultry-keeping equipment, bee-keeping, fish farming',
                'poultry equipment, agricultural machine, fish farming equipment',
                null],

            ['84', 'Machinery', '8443', 'Printing machinery', '8443', 10.00, '2',
                'Printing presses, photocopiers, inkjet printers',
                'printer, printing machine, photocopier, copier, plotter, inkjet, laser printer',
                null],

            ['84', 'Machinery', '8450', 'Washing machines', '8450', 20.00, '3',
                'Household or laundry-type washing machines',
                'washing machine, washer, laundry machine, front loader, top loader',
                null],

            ['84', 'Machinery', '8451', 'Industrial washing/drying machines', '8451', 10.00, '2',
                'Commercial laundry, ironing, dry-cleaning machines',
                'industrial washing machine, commercial dryer, laundry equipment',
                null],

            ['84', 'Machinery', '8452', 'Sewing machines', '8452', 20.00, '3',
                'Domestic and industrial sewing machines',
                'sewing machine, industrial sewing, overlock machine, embroidery',
                null],

            ['84', 'Machinery', '8467', 'Pneumatic/motor tools', '8467', 20.00, '3',
                'Power drills, grinders, sanders',
                'power drill, grinder, sander, electric drill, angle grinder, power tools',
                null],

            ['84', 'Machinery', '8471', 'Automatic data processing machines', '8471', 0.00, '0',
                'Computers, laptops, tablets, servers',
                'computer, laptop, desktop, server, tablet, PC, notebook, computing',
                'Zero duty on computers and computing equipment'],

            ['84', 'Machinery', '8473', 'Computer parts and accessories', '8473', 0.00, '0',
                'Parts and accessories for computers',
                'computer parts, RAM, hard drive, motherboard, keyboard, mouse, printer parts',
                null],

            ['84', 'Machinery', '8474', 'Sorting/crushing/mixing machinery', '8474', 10.00, '2',
                'Rock crushers, concrete mixers, screening machines',
                'crusher, concrete mixer, screening machine, quarry machine',
                null],

            ['84', 'Machinery', '8477', 'Rubber/plastics processing machinery', '8477', 10.00, '2',
                'Injection moulding, extrusion, blow moulding machines',
                'injection moulding, extrusion machine, blow moulding, plastic machine',
                null],

            ['84', 'Machinery', '8479', 'Machines with individual functions NEC', '8479', 10.00, '2',
                'Industrial robots, machines for specific functions',
                'industrial robot, machine, mechanical equipment, automation',
                null],

            ['84', 'Machinery', '8481', 'Taps, cocks, valves', '8481', 20.00, '3',
                'Valves, taps, cocks, faucets for pipes',
                'valve, tap, cock, faucet, pipe fitting, plumbing valve, ball valve',
                null],

            ['84', 'Machinery', '8483', 'Transmission shafts and bearings', '8483', 10.00, '2',
                'Transmission shafts, cranks, bearing housings, gears',
                'bearing, gear, shaft, transmission, gearbox, crankshaft',
                null],

            ['84', 'Machinery', '8484', 'Gaskets and seals', '8484', 20.00, '3',
                'Metallic gaskets, mechanical seals',
                'gasket, seal, mechanical seal, O-ring, flange gasket',
                null],

            ['85', 'Electrical equipment', '8501', 'Electric motors and generators', '8501', 10.00, '2',
                'AC/DC motors, alternators, generator sets',
                'electric motor, generator, alternator, dynamo, motor',
                null],

            ['85', 'Electrical equipment', '8502', 'Electric generating sets', '8502', 10.00, '2',
                'Generating sets, rotary converters',
                'generator set, genset, power generator, standby generator',
                null],

            ['85', 'Electrical equipment', '8504', 'Transformers, static converters', '8504', 10.00, '2',
                'Transformers, inverters, rectifiers, UPS',
                'transformer, inverter, UPS, rectifier, power supply, static converter',
                null],

            ['85', 'Electrical equipment', '8506', 'Primary cells and batteries', '8506', 20.00, '3',
                'Dry cell batteries, button cells, lithium batteries',
                'battery, dry cell, AA battery, torch battery, alkaline battery',
                null],

            ['85', 'Electrical equipment', '8507', 'Electric accumulators', '8507', 20.00, '3',
                'Lead acid batteries, lithium-ion battery packs',
                'car battery, lead acid battery, lithium battery, rechargeable battery, power bank',
                null],

            ['85', 'Electrical equipment', '8516', 'Electrical domestic appliances', '8516', 20.00, '3',
                'Electric water heaters, hair dryers, irons, toasters, kettles',
                'electric kettle, iron, hair dryer, toaster, water heater, microwave, hot plate',
                null],

            ['85', 'Electrical equipment', '8517', 'Telephone sets, communication apparatus', '8517', 20.00, '3',
                'Telephones, smartphones, routers, modems, switches',
                'phone, smartphone, mobile phone, telephone, router, modem, network switch, telecoms',
                null],

            ['85', 'Electrical equipment', '8518', 'Microphones, loudspeakers, amplifiers', '8518', 20.00, '3',
                'Microphones, speakers, headphones, amplifiers, earphones',
                'speaker, microphone, amplifier, headphone, earphone, audio equipment, sound system',
                null],

            ['85', 'Electrical equipment', '8519', 'Sound recording/reproducing apparatus', '8519', 20.00, '3',
                'CD players, turntables, cassette players',
                'CD player, DVD player, media player, turntable, stereo',
                null],

            ['85', 'Electrical equipment', '8521', 'Video recording/reproducing apparatus', '8521', 20.00, '3',
                'Video players, CCTV recorders, video cameras',
                'CCTV, DVR, NVR, video recorder, video camera, surveillance',
                null],

            ['85', 'Electrical equipment', '8525', 'Transmission apparatus', '8525', 10.00, '2',
                'Radio transmitters, TV cameras, broadcast equipment',
                'radio transmitter, broadcast equipment, TV camera, antenna equipment',
                null],

            ['85', 'Electrical equipment', '8527', 'Reception apparatus for radio', '8527', 20.00, '3',
                'Radio receivers, car radios, combined radio-clock',
                'radio, car radio, AM FM radio, radio receiver',
                null],

            ['85', 'Electrical equipment', '8528', 'Monitors and television sets', '8528', 20.00, '3',
                'Television sets, computer monitors, projectors',
                'TV, television, monitor, LCD, LED, plasma, projector, screen, display',
                null],

            ['85', 'Electrical equipment', '8535', 'Electrical switchgear >1000V', '8535', 10.00, '2',
                'High voltage circuit breakers, switches, fuses',
                'circuit breaker, switchgear, high voltage switch, fuse gear',
                null],

            ['85', 'Electrical equipment', '8536', 'Electrical switchgear ≤1000V', '8536', 10.00, '2',
                'Low voltage switches, sockets, fuses, relays',
                'socket, switch, plug, fuse, relay, MCB, circuit breaker, electrical fittings',
                null],

            ['85', 'Electrical equipment', '8544', 'Insulated wire and cable', '8544', 10.00, '2',
                'Electrical cables, wires, coaxial cables, fibre optic',
                'electrical cable, wire, coaxial cable, fibre optic, power cable, armoured cable',
                null],

            ['85', 'Electrical equipment', '8548', 'Electrical parts and waste', '8548', 10.00, '2',
                'Battery waste, spent primary cells',
                'battery waste, electrical parts, spent battery',
                null],

            // ════════════════════════════════════════════════════════════════
            // SECTION XVII — VEHICLES & TRANSPORT (Ch 86–89)
            // ════════════════════════════════════════════════════════════════

            ['87', 'Vehicles', '8701', 'Tractors', '8701', 10.00, '2',
                'Agricultural and road tractors',
                'tractor, farm tractor, agricultural tractor, road tractor',
                'Excludes: tractors of heading 8709'],

            ['87', 'Vehicles', '8702', 'Motor vehicles for 10+ persons', '8702', 20.00, '3',
                'Buses, minibuses, coaches',
                'bus, minibus, coach, trotro, vehicle for hire, bus body, passenger bus',
                null],

            ['87', 'Vehicles', '8703', 'Motor cars — passenger', '8703', 20.00, '3',
                'Motor cars and other vehicles principally for transport of persons',
                'car, sedan, SUV, pickup, passenger vehicle, motor car, saloon, hatchback, station wagon, Toyota, Hyundai, Honda, Ford, Nissan, Volkswagen, BMW, Mercedes, Kia, Mazda, Land Cruiser, RAV4, Camry, Corolla, Fortuner, Highlander',
                'Most common heading for imported vehicles — applies to cars, SUVs, 4x4s. Excludes buses (8702) and goods vehicles (8704)'],

            ['87', 'Vehicles', '8704', 'Motor vehicles for goods transport', '8704', 20.00, '3',
                'Trucks, lorries, vans for goods transport',
                'truck, lorry, van, pickup truck, delivery truck, cargo van, goods vehicle',
                'Excludes: passenger vehicles (8703). Key distinction: primary purpose must be goods transport'],

            ['87', 'Vehicles', '8705', 'Special purpose vehicles', '8705', 20.00, '3',
                'Breakdown lorries, crane lorries, concrete mixers, ambulances',
                'ambulance, concrete mixer truck, crane truck, fire truck, tipper, dumper, special vehicle',
                null],

            ['87', 'Vehicles', '8706', 'Chassis with engines', '8706', 20.00, '3',
                'Chassis fitted with engines for motor vehicles',
                'chassis, vehicle chassis, truck chassis, bus chassis',
                null],

            ['87', 'Vehicles', '8707', 'Bodies for motor vehicles', '8707', 20.00, '3',
                'Car bodies, cab bodies, van bodies',
                'car body, vehicle body, cab, van body',
                null],

            ['87', 'Vehicles', '8708', 'Parts and accessories for vehicles', '8708', 20.00, '3',
                'Bumpers, doors, bonnets, windows, seats, seatbelts for vehicles',
                'car parts, spare parts, bumper, door, bonnet, windscreen, seat, seatbelt, fender, exhaust, brake parts',
                'Very important heading — agents often argue between 8708 and commodity-specific headings for components'],

            ['87', 'Vehicles', '8709', 'Works trucks, not fitted for roads', '8709', 20.00, '3',
                'Airport luggage trucks, warehouse tractors',
                'works truck, airport truck, warehouse tractor, luggage cart',
                null],

            ['87', 'Vehicles', '8711', 'Motorcycles and mopeds', '8711', 20.00, '3',
                'Motorcycles, mopeds, scooters',
                'motorcycle, motorbike, moped, scooter, bike, okada',
                null],

            ['87', 'Vehicles', '8712', 'Bicycles', '8712', 20.00, '3',
                'Bicycles and other cycles',
                'bicycle, bike, cycle, mountain bike, road bike',
                null],

            ['87', 'Vehicles', '8714', 'Parts for motorcycles/bicycles', '8714', 20.00, '3',
                'Parts and accessories for motorcycles and bicycles',
                'motorcycle parts, bicycle parts, tyre, chain, brake, pedal',
                null],

            ['87', 'Vehicles', '8716', 'Trailers and semi-trailers', '8716', 20.00, '3',
                'Semi-trailers, cargo trailers, tanker trailers',
                'trailer, semi-trailer, cargo trailer, tanker, flatbed trailer',
                null],

            ['88', 'Aircraft', '8801', 'Balloons and dirigibles', '8801', 5.00, '1',
                'Hot air balloons, airships',
                'balloon, airship, dirigible',
                null],

            ['88', 'Aircraft', '8802', 'Aircraft, spacecraft', '8802', 5.00, '1',
                'Aeroplanes, helicopters, gliders, drones',
                'aircraft, aeroplane, helicopter, drone, UAV, glider',
                null],

            ['89', 'Ships and boats', '8901', 'Cruise ships, cargo vessels', '8901', 5.00, '1',
                'Ocean-going vessels for passengers or cargo',
                'ship, vessel, cargo ship, container ship, tanker, bulk carrier',
                null],

            ['89', 'Ships and boats', '8903', 'Yachts and pleasure craft', '8903', 20.00, '3',
                'Yachts, motorboats, canoes for leisure',
                'yacht, boat, motorboat, canoe, speedboat, pleasure craft',
                null],

            // ════════════════════════════════════════════════════════════════
            // SECTION XVIII — OPTICAL, MEDICAL, CLOCKS (Ch 90–92)
            // ════════════════════════════════════════════════════════════════

            ['90', 'Optical/medical instruments', '9001', 'Optical fibres, lenses', '9001', 10.00, '2',
                'Optical fibres, optical fibre bundles, lenses, prisms',
                'optical fibre, lens, prism, optical equipment',
                null],

            ['90', 'Optical/medical instruments', '9006', 'Cameras', '9006', 20.00, '3',
                'Photographic cameras — film and digital',
                'camera, digital camera, film camera, DSLR, mirrorless camera',
                null],

            ['90', 'Optical/medical instruments', '9013', 'Lasers, microscopes, optical instruments', '9013', 10.00, '2',
                'Lasers, liquid crystal devices, other optical instruments',
                'laser, microscope, optical instrument, LCD device',
                null],

            ['90', 'Optical/medical instruments', '9018', 'Medical instruments', '9018', 0.00, '0',
                'Syringes, needles, stethoscopes, blood pressure monitors, ECG machines',
                'syringe, needle, stethoscope, medical instrument, ECG, blood pressure, surgical instrument',
                'Zero duty on medical instruments to support healthcare'],

            ['90', 'Optical/medical instruments', '9019', 'Mechano-therapy appliances', '9019', 0.00, '0',
                'Massage apparatus, physiotherapy, oxygen equipment',
                'oxygen, nebulizer, physiotherapy, massage machine, CPAP',
                null],

            ['90', 'Optical/medical instruments', '9021', 'Orthopaedic appliances', '9021', 0.00, '0',
                'Crutches, surgical belts, hearing aids, pacemakers',
                'crutch, hearing aid, pacemaker, prosthetic, wheelchair, orthopaedic',
                null],

            ['91', 'Clocks and watches', '9101', 'Wrist watches with precious metals', '9101', 20.00, '3',
                'Wrist-watches with gold or silver cases',
                'watch, wristwatch, luxury watch',
                null],

            ['91', 'Clocks and watches', '9102', 'Wrist watches, other', '9102', 20.00, '3',
                'Wrist-watches — standard',
                'watch, wristwatch, smartwatch, digital watch',
                null],

            ['91', 'Clocks and watches', '9105', 'Other clocks', '9105', 20.00, '3',
                'Alarm clocks, wall clocks, instrument panel clocks',
                'clock, alarm clock, wall clock, table clock',
                null],

            // ════════════════════════════════════════════════════════════════
            // SECTION XX — MISCELLANEOUS MANUFACTURES (Ch 94–96)
            // ════════════════════════════════════════════════════════════════

            ['94', 'Furniture, bedding, lamps', '9401', 'Seats', '9401', 20.00, '3',
                'Chairs, sofas, settees, car seats',
                'chair, sofa, settee, seat, office chair, car seat, armchair, couch',
                null],

            ['94', 'Furniture, bedding, lamps', '9403', 'Other furniture', '9403', 20.00, '3',
                'Office furniture, kitchen furniture, bedroom furniture',
                'furniture, table, desk, wardrobe, cabinet, shelving, office furniture, bed frame',
                null],

            ['94', 'Furniture, bedding, lamps', '9404', 'Mattresses and bedding', '9404', 20.00, '3',
                'Mattresses, sleeping bags, quilts, pillows',
                'mattress, pillow, quilt, duvet, sleeping bag, bedding, foam mattress',
                null],

            ['94', 'Furniture, bedding, lamps', '9405', 'Lamps and lighting fittings', '9405', 20.00, '3',
                'Chandeliers, LED lights, street lamps, floor lamps',
                'lamp, light, LED light, chandelier, street light, bulb, spotlight, lighting fitting',
                null],

            ['95', 'Toys, games, sports', '9503', 'Toys', '9503', 20.00, '3',
                'Tricycles, dolls, toy cars, puzzles, board games',
                'toy, doll, toy car, puzzle, board game, action figure, LEGO',
                null],

            ['95', 'Toys, games, sports', '9504', 'Video games, gambling machines', '9504', 20.00, '3',
                'Video game consoles, game cartridges, arcade machines',
                'PlayStation, Xbox, Nintendo, game console, video game, gaming',
                null],

            ['95', 'Toys, games, sports', '9506', 'Sports equipment', '9506', 20.00, '3',
                'Exercise equipment, sports goods, gym equipment',
                'gym equipment, treadmill, exercise bike, sports goods, football, sports',
                null],

            ['96', 'Miscellaneous', '9601', 'Worked ivory, bone, shell', '9601', 20.00, '3',
                'Articles of ivory, tortoiseshell, bone, shell',
                'ivory, bone article, shell, craft',
                null],

            ['96', 'Miscellaneous', '9603', 'Brooms, brushes, mops', '9603', 20.00, '3',
                'Brooms, brushes, mops, squeegees',
                'broom, brush, mop, squeegee, cleaning brush, paintbrush',
                null],

            ['96', 'Miscellaneous', '9606', 'Buttons, press fasteners', '9606', 20.00, '3',
                'Buttons, press studs, snap fasteners',
                'button, press stud, fastener, zip, zipper',
                null],

            ['96', 'Miscellaneous', '9608', 'Ball pens, markers, fountain pens', '9608', 20.00, '3',
                'Ballpoint pens, felt-tip pens, markers, fountain pens',
                'pen, ballpoint pen, marker, highlighter, fountain pen, biro',
                null],

            ['96', 'Miscellaneous', '9613', 'Cigarette lighters', '9613', 20.00, '3',
                'Pocket lighters, gas lighters',
                'lighter, cigarette lighter, gas lighter, disposable lighter',
                null],

        ];

        // ── Insert all records ────────────────────────────────────────────────
        foreach ($codes as $code) {
            DB::table('hs_codes')->updateOrInsert(
                ['HSCode' => $code[4]],
                [
                    'Chapter' => $code[0],
                    'ChapterDesc' => $code[1],
                    'Heading' => $code[2],
                    'HeadingDesc' => $code[3],
                    'HSCode' => $code[4],
                    'ImportDutyRate' => $code[5],
                    'ECOWASBand' => $code[6],
                    'Notes' => $code[7],
                    'Keywords' => $code[8],
                    'Exclusions' => $code[9],
                    'Inclusions' => null,
                    'IsActive' => true,
                    'VATRate' => 15.00,
                    'NHILRate' => 2.50,
                    'GETFundRate' => 2.50,
                    'COVIDRate' => 1.00,
                    'ECOWASRate' => 0.50,
                    'AURate' => 0.20,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        $this->command->info('HS Codes seeded: '.count($codes).' headings.');
    }
}
