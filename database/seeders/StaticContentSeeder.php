<?php

namespace Database\Seeders;

use App\Models\StaticContent;
use Illuminate\Database\Seeder;

class StaticContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Terms and Conditions - Swahili
        StaticContent::updateOrCreate(
            ['type' => 'terms', 'language' => 'sw'],
            [
                'title' => 'Sheria na Masharti',
                'content' => $this->getTermsSwahili(),
                'version' => '1.0',
                'effective_date' => '2024-01-01',
                'is_active' => true,
            ]
        );

        // Terms and Conditions - English
        StaticContent::updateOrCreate(
            ['type' => 'terms', 'language' => 'en'],
            [
                'title' => 'Terms and Conditions',
                'content' => $this->getTermsEnglish(),
                'version' => '1.0',
                'effective_date' => '2024-01-01',
                'is_active' => true,
            ]
        );

        // Privacy Policy - Swahili
        StaticContent::updateOrCreate(
            ['type' => 'privacy', 'language' => 'sw'],
            [
                'title' => 'Sera ya Faragha',
                'content' => $this->getPrivacySwahili(),
                'version' => '1.0',
                'effective_date' => '2024-01-01',
                'is_active' => true,
            ]
        );

        // Privacy Policy - English
        StaticContent::updateOrCreate(
            ['type' => 'privacy', 'language' => 'en'],
            [
                'title' => 'Privacy Policy',
                'content' => $this->getPrivacyEnglish(),
                'version' => '1.0',
                'effective_date' => '2024-01-01',
                'is_active' => true,
            ]
        );

        // About - Swahili
        StaticContent::updateOrCreate(
            ['type' => 'about', 'language' => 'sw'],
            [
                'title' => 'Kuhusu Kiasi Daily',
                'content' => $this->getAboutSwahili(),
                'version' => '1.0.0',
                'is_active' => true,
            ]
        );

        // About - English
        StaticContent::updateOrCreate(
            ['type' => 'about', 'language' => 'en'],
            [
                'title' => 'About Kiasi Daily',
                'content' => $this->getAboutEnglish(),
                'version' => '1.0.0',
                'is_active' => true,
            ]
        );
    }

    private function getTermsSwahili(): string
    {
        return <<<'CONTENT'
## 1. Utangulizi

Karibu Kiasi Daily! Kwa kutumia programu yetu, unakubali sheria na masharti haya. Tafadhali yasome kwa makini.

## 2. Huduma Zetu

Kiasi Daily ni programu ya kusimamia matumizi na mapato yako ya kibinafsi. Tunakusaidia:
- Kufuatilia matumizi yako ya kila siku
- Kuweka na kusimamia bajeti
- Kupata takwimu za fedha zako
- Kuweka malengo ya kifedha

## 3. Akaunti Yako

- Unahusika na kulinda taarifa za akaunti yako
- Usishiriki nywila yako na mtu mwingine
- Tuarifu mara moja ukiona shughuli zisizo za kawaida

## 4. Matumizi Yanayokubalika

Unakubali kutumia Kiasi Daily kwa madhumuni halali tu. Hauruhusiwi:
- Kutumia programu kwa njia zisizo halali
- Kujaribu kuingilia mifumo yetu
- Kushiriki taarifa za uongo

## 5. Faragha

Tunalinda faragha yako. Soma Sera yetu ya Faragha kwa maelezo zaidi kuhusu jinsi tunavyoshughulikia taarifa zako.

## 6. Mabadiliko

Tunaweza kubadilisha masharti haya wakati wowote. Tutakujulisha kuhusu mabadiliko muhimu.

## 7. Wasiliana Nasi

Kwa maswali yoyote, wasiliana nasi kupitia support@kiasidaily.com
CONTENT;
    }

    private function getTermsEnglish(): string
    {
        return <<<'CONTENT'
## 1. Introduction

Welcome to Kiasi Daily! By using our app, you agree to these terms and conditions. Please read them carefully.

## 2. Our Services

Kiasi Daily is a personal finance management app. We help you:
- Track your daily expenses and income
- Set and manage budgets
- Get financial insights and statistics
- Set financial goals

## 3. Your Account

- You are responsible for protecting your account information
- Do not share your password with others
- Notify us immediately if you notice unusual activity

## 4. Acceptable Use

You agree to use Kiasi Daily for lawful purposes only. You may not:
- Use the app for illegal purposes
- Attempt to interfere with our systems
- Share false information

## 5. Privacy

We protect your privacy. Read our Privacy Policy for details on how we handle your information.

## 6. Changes

We may change these terms at any time. We will notify you of significant changes.

## 7. Contact Us

For any questions, contact us at support@kiasidaily.com
CONTENT;
    }

    private function getPrivacySwahili(): string
    {
        return <<<'CONTENT'
## 1. Taarifa Tunazokusanya

Kiasi Daily inakusanya taarifa zifuatazo:
- **Taarifa za Akaunti**: Jina, barua pepe
- **Taarifa za Fedha**: Matumizi, mapato, kategoria
- **Taarifa za Kifaa**: Aina ya kifaa, toleo la programu

## 2. Jinsi Tunavyotumia Taarifa

Tunatumia taarifa zako kwa:
- Kutoa huduma zetu
- Kuboresha uzoefu wako
- Kutuma arifa muhimu
- Kulinda usalama wa akaunti yako

## 3. Ulinzi wa Taarifa

Tunalinda taarifa zako kwa:
- Encryption ya data
- Seva salama
- Ufikiaji uliodhibitiwa

## 4. Kushiriki Taarifa

Hatushiriki taarifa zako na watu wa nje isipokuwa:
- Unapotoa ruhusa
- Inapohitajika kisheria
- Kwa huduma muhimu za kiufundi

## 5. Haki Zako

Una haki ya:
- Kufikia taarifa zako
- Kusahihisha taarifa zako
- Kufuta akaunti yako
- Kupakua data yako

## 6. Wasiliana Nasi

Kwa maswali ya faragha: privacy@kiasidaily.com
CONTENT;
    }

    private function getPrivacyEnglish(): string
    {
        return <<<'CONTENT'
## 1. Information We Collect

Kiasi Daily collects the following information:
- **Account Information**: Name, email
- **Financial Data**: Expenses, income, categories
- **Device Information**: Device type, app version

## 2. How We Use Information

We use your information to:
- Provide our services
- Improve your experience
- Send important notifications
- Protect your account security

## 3. Data Protection

We protect your information through:
- Data encryption
- Secure servers
- Controlled access

## 4. Information Sharing

We do not share your information with third parties except:
- When you give permission
- When required by law
- For essential technical services

## 5. Your Rights

You have the right to:
- Access your data
- Correct your data
- Delete your account
- Download your data

## 6. Contact Us

For privacy questions: privacy@kiasidaily.com
CONTENT;
    }

    private function getAboutSwahili(): string
    {
        return <<<'CONTENT'
Kiasi Daily ni programu ya kusimamia matumizi yako ya kila siku. Iliundwa kwa ajili ya Watanzania ambao wanataka kudhibiti fedha zao kwa urahisi.

## Dhamira Yetu

Kusaidia kila Mtanzania kuwa na udhibiti bora wa fedha zake za kila siku kupitia teknolojia rahisi na inayofaa.

## Huduma Zetu

- **Kufuatilia Matumizi**: Rekodi matumizi yako kwa urahisi
- **Bajeti**: Weka na simamia bajeti yako
- **Takwimu**: Pata picha kamili ya hali yako ya kifedha
- **Arifa**: Pokea vikumbusho vya bajeti yako

## Timu Yetu

Kiasi Daily iliundwa na timu ya Watanzania wanaopenda teknolojia na fedha za kibinafsi.
CONTENT;
    }

    private function getAboutEnglish(): string
    {
        return <<<'CONTENT'
Kiasi Daily is a daily expense management app. It was created for Tanzanians who want to easily control their finances.

## Our Mission

To help every Tanzanian have better control of their daily finances through simple and effective technology.

## Our Services

- **Expense Tracking**: Easily record your expenses
- **Budgeting**: Set and manage your budget
- **Statistics**: Get a complete picture of your financial health
- **Notifications**: Receive budget reminders

## Our Team

Kiasi Daily was created by a team of Tanzanians passionate about technology and personal finance.
CONTENT;
    }
}

