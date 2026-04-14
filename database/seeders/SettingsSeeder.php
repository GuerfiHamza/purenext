<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // ── Informations entreprise ───────────────────────────
            ['key' => 'company_name',       'value' => 'PURENEXT SARL',              'type' => 'string', 'group' => 'company', 'label' => 'Nom de l\'entreprise'],
            ['key' => 'company_address',    'value' => 'Alger, Algérie',             'type' => 'string', 'group' => 'company', 'label' => 'Adresse'],
            ['key' => 'company_phone',      'value' => '+213 549 90 42 01',          'type' => 'string', 'group' => 'company', 'label' => 'Téléphone'],
            ['key' => 'company_email',      'value' => 'contact@purenext.dz',        'type' => 'string', 'group' => 'company', 'label' => 'Email'],
            ['key' => 'company_website',    'value' => 'www.purenext.dz',            'type' => 'string', 'group' => 'company', 'label' => 'Site web'],
            ['key' => 'company_rc',         'value' => '',                           'type' => 'string', 'group' => 'company', 'label' => 'N° Registre Commerce'],
            ['key' => 'company_nif',        'value' => '',                           'type' => 'string', 'group' => 'company', 'label' => 'NIF'],
            ['key' => 'company_nis',        'value' => '',                           'type' => 'string', 'group' => 'company', 'label' => 'NIS'],
            ['key' => 'company_rib',        'value' => '',                           'type' => 'string', 'group' => 'company', 'label' => 'RIB Bancaire'],
            ['key' => 'company_bank',       'value' => '',                           'type' => 'string', 'group' => 'company', 'label' => 'Banque'],
            ['key' => 'company_ai', 'value' => '', 'type' => 'string', 'group' => 'company', 'label' => 'Article d\'Imposition (AI)'],  
            // ── Paramètres facturation ────────────────────────────
            ['key' => 'invoice_tva_rate',   'value' => '19',                         'type' => 'number', 'group' => 'invoice', 'label' => 'Taux TVA (%)'],
            ['key' => 'invoice_tva_enabled','value' => '1',                          'type' => 'boolean','group' => 'invoice', 'label' => 'Activer la TVA'],
            ['key' => 'invoice_prefix',     'value' => 'FACT',                       'type' => 'string', 'group' => 'invoice', 'label' => 'Préfixe facture'],
            ['key' => 'invoice_notes',      'value' => 'Merci pour votre confiance.','type' => 'string', 'group' => 'invoice', 'label' => 'Mention pied de facture'],
            ['key' => 'invoice_payment',    'value' => 'Paiement à 30 jours.',       'type' => 'string', 'group' => 'invoice', 'label' => 'Conditions de paiement'],
            ['key' => 'delivery_notes',     'value' => 'Marchandise voyageant aux risques et périls du destinataire.', 'type' => 'string', 'group' => 'invoice', 'label' => 'Mention bon de livraison'],

            // ── Paramètres bons de commande ───────────────────────
            ['key' => 'po_prefix',          'value' => 'BC',                         'type' => 'string', 'group' => 'purchase', 'label' => 'Préfixe bon de commande'],
            ['key' => 'po_notes',           'value' => 'Veuillez confirmer réception de ce bon de commande.', 'type' => 'string', 'group' => 'purchase', 'label' => 'Mention bon de commande'],
        ];

        foreach ($settings as $setting) {
            Setting::updateOrCreate(['key' => $setting['key']], $setting);
        }
    }
}