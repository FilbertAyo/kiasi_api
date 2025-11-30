<?php

namespace Database\Seeders;

use App\Models\FaqCategory;
use App\Models\FaqQuestion;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create categories
        $gettingStarted = FaqCategory::updateOrCreate(
            ['slug' => 'getting_started'],
            [
                'name_en' => 'Getting Started',
                'name_sw' => 'Kuanza',
                'icon' => 'rocket',
                'display_order' => 1,
                'is_active' => true,
            ]
        );

        $transactions = FaqCategory::updateOrCreate(
            ['slug' => 'transactions'],
            [
                'name_en' => 'Transactions',
                'name_sw' => 'Miamala',
                'icon' => 'receipt',
                'display_order' => 2,
                'is_active' => true,
            ]
        );

        $account = FaqCategory::updateOrCreate(
            ['slug' => 'account'],
            [
                'name_en' => 'Account',
                'name_sw' => 'Akaunti',
                'icon' => 'person',
                'display_order' => 3,
                'is_active' => true,
            ]
        );

        $budgets = FaqCategory::updateOrCreate(
            ['slug' => 'budgets'],
            [
                'name_en' => 'Budgets',
                'name_sw' => 'Bajeti',
                'icon' => 'wallet',
                'display_order' => 4,
                'is_active' => true,
            ]
        );

        // Getting Started - Swahili
        $this->createQuestion($gettingStarted->id, 'sw', 1,
            'Jinsi ya kuanza kutumia Kiasi Daily?',
            'Baada ya kufungua akaunti, unaweza kuanza kurekodi matumizi yako mara moja. Bonyeza kitufe cha + chini ya skrini kuongeza matumizi au mapato yako ya kwanza. Chagua kategoria, ingiza kiasi, na uhifadhi!'
        );

        $this->createQuestion($gettingStarted->id, 'sw', 2,
            'Je, ninaweza kutumia programu bila mtandao?',
            'Ndiyo, Kiasi Daily inafanya kazi bila mtandao! Taarifa zako zote zimehifadhiwa kwenye kifaa chako. Mtandao unahitajika tu wakati wa kuingia kwa mara ya kwanza na kusawazisha data.'
        );

        // Getting Started - English
        $this->createQuestion($gettingStarted->id, 'en', 1,
            'How do I start using Kiasi Daily?',
            'After creating an account, you can start recording your expenses immediately. Tap the + button at the bottom of the screen to add your first expense or income. Select a category, enter the amount, and save!'
        );

        $this->createQuestion($gettingStarted->id, 'en', 2,
            'Can I use the app without internet?',
            'Yes, Kiasi Daily works offline! All your data is stored on your device. Internet is only needed when logging in for the first time and syncing data.'
        );

        // Transactions - Swahili
        $this->createQuestion($transactions->id, 'sw', 1,
            'Jinsi ya kuongeza matumizi mapya?',
            'Bonyeza kitufe cha + chini ya skrini, chagua "Matumizi", ingiza kiasi na maelezo, chagua kategoria, na bonyeza "Hifadhi". Unaweza pia kuongeza picha ya risiti ukitaka.'
        );

        $this->createQuestion($transactions->id, 'sw', 2,
            'Jinsi ya kuhariri au kufuta muamala?',
            'Bonyeza muamala unaotaka kuhariri kwenye orodha. Utaona chaguzi za kuhariri au kufuta. Kumbuka kwamba mara unapofuta muamala, hauwezi kuurejesha.'
        );

        $this->createQuestion($transactions->id, 'sw', 3,
            'Ninaweza kuongeza kategoria zangu mwenyewe?',
            'Ndiyo! Nenda Mipangilio > Kategoria > Ongeza Kategoria. Unaweza kuweka jina na icon ya kategoria yako mpya.'
        );

        // Transactions - English
        $this->createQuestion($transactions->id, 'en', 1,
            'How do I add a new expense?',
            'Tap the + button at the bottom of the screen, select "Expense", enter the amount and description, choose a category, and tap "Save". You can also add a receipt photo if you want.'
        );

        $this->createQuestion($transactions->id, 'en', 2,
            'How do I edit or delete a transaction?',
            'Tap on the transaction you want to edit in the list. You will see options to edit or delete. Remember that once you delete a transaction, it cannot be recovered.'
        );

        $this->createQuestion($transactions->id, 'en', 3,
            'Can I add my own categories?',
            'Yes! Go to Settings > Categories > Add Category. You can set a name and icon for your new category.'
        );

        // Account - Swahili
        $this->createQuestion($account->id, 'sw', 1,
            'Jinsi ya kubadilisha nywila yangu?',
            'Nenda Mipangilio > Usalama > Badilisha Nywila. Utahitaji kuingiza nywila yako ya sasa, kisha nywila mpya mara mbili.'
        );

        $this->createQuestion($account->id, 'sw', 2,
            'Jinsi ya kufuta akaunti yangu?',
            'Nenda Mipangilio > Akaunti > Futa Akaunti. Utahitaji kuingiza nywila yako kuthibitisha. Kumbuka kwamba taarifa zote zitafutwa na haziwezi kurejeshwa.'
        );

        // Account - English
        $this->createQuestion($account->id, 'en', 1,
            'How do I change my password?',
            'Go to Settings > Security > Change Password. You will need to enter your current password, then your new password twice.'
        );

        $this->createQuestion($account->id, 'en', 2,
            'How do I delete my account?',
            'Go to Settings > Account > Delete Account. You will need to enter your password to confirm. Remember that all data will be deleted and cannot be recovered.'
        );

        // Budgets - Swahili
        $this->createQuestion($budgets->id, 'sw', 1,
            'Jinsi ya kuweka bajeti?',
            'Nenda kichupo cha Bajeti na bonyeza "Ongeza Bajeti". Chagua kategoria, weka kiasi cha juu, na kipindi (wiki/mwezi). Utapokea arifa unapokaribia kikomo chako.'
        );

        $this->createQuestion($budgets->id, 'sw', 2,
            'Nitajuaje nimezidi bajeti yangu?',
            'Utapokea arifa moja kwa moja ukifikia 80% na 100% ya bajeti yako. Pia unaweza kuona hali ya bajeti zako zote kwenye kichupo cha Bajeti.'
        );

        // Budgets - English
        $this->createQuestion($budgets->id, 'en', 1,
            'How do I set a budget?',
            'Go to the Budget tab and tap "Add Budget". Select a category, set the maximum amount, and the period (week/month). You will receive notifications when you approach your limit.'
        );

        $this->createQuestion($budgets->id, 'en', 2,
            'How will I know if I exceed my budget?',
            'You will receive automatic notifications when you reach 80% and 100% of your budget. You can also see the status of all your budgets in the Budget tab.'
        );
    }

    private function createQuestion(int $categoryId, string $language, int $order, string $question, string $answer): void
    {
        FaqQuestion::updateOrCreate(
            [
                'category_id' => $categoryId,
                'language' => $language,
                'question' => $question,
            ],
            [
                'answer' => $answer,
                'display_order' => $order,
                'is_active' => true,
            ]
        );
    }
}

