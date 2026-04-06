<?php

namespace App\Libraries;

/**
 * Business sectors and activity categories
 */
class BusinessSectors
{
    public static function getAll(): array
    {
        return [
            'technology' => [
                'label' => 'Technology (Technologie)',
                'subcategories' => [
                    'software' => 'Software & SaaS',
                    'hardware' => 'Hardware & Electronics',
                    'ai_ml' => 'Artificial Intelligence & Machine Learning',
                    'cloud' => 'Cloud Computing & Infrastructure',
                    'cybersecurity' => 'Cybersecurity',
                ]
            ],
            'finance' => [
                'label' => 'Finance & Banking (Finance)',
                'subcategories' => [
                    'banking' => 'Banking',
                    'insurance' => 'Insurance',
                    'investment' => 'Investment & Trading',
                    'fintech' => 'FinTech',
                    'accounting' => 'Accounting & Auditing',
                ]
            ],
            'healthcare' => [
                'label' => 'Healthcare & Pharmaceuticals (Santé)',
                'subcategories' => [
                    'hospitals' => 'Hospitals & Medical Clinics',
                    'pharmaceuticals' => 'Pharmaceuticals',
                    'medical_devices' => 'Medical Devices',
                    'biotech' => 'Biotechnology',
                    'mental_health' => 'Mental Health & Wellness',
                ]
            ],
            'manufacturing' => [
                'label' => 'Manufacturing (Industrie)',
                'subcategories' => [
                    'automotive' => 'Automotive',
                    'electronics' => 'Electronics Manufacturing',
                    'textiles' => 'Textiles & Apparel',
                    'chemicals' => 'Chemicals & Plastics',
                    'food_beverage' => 'Food & Beverage',
                ]
            ],
            'retail' => [
                'label' => 'Retail & E-Commerce (Commerce)',
                'subcategories' => [
                    'ecommerce' => 'E-Commerce',
                    'department_stores' => 'Department Stores',
                    'specialty_retail' => 'Specialty Retail',
                    'fashion' => 'Fashion & Luxury Goods',
                    'groceries' => 'Groceries & Food Retail',
                ]
            ],
            'real_estate' => [
                'label' => 'Real Estate & Construction (Immobilier)',
                'subcategories' => [
                    'residential' => 'Residential Development',
                    'commercial' => 'Commercial Real Estate',
                    'construction' => 'Construction',
                    'architecture' => 'Architecture & Design',
                    'property_management' => 'Property Management',
                ]
            ],
            'energy' => [
                'label' => 'Energy (Énergie)',
                'subcategories' => [
                    'oil_gas' => 'Oil & Gas',
                    'renewable' => 'Renewable Energy',
                    'utilities' => 'Utilities',
                    'mining' => 'Mining',
                ]
            ],
            'transportation' => [
                'label' => 'Transportation & Logistics (Transport)',
                'subcategories' => [
                    'airlines' => 'Airlines & Aviation',
                    'shipping' => 'Shipping & Maritime',
                    'logistics' => 'Logistics & Warehousing',
                    'railways' => 'Railways',
                    'public_transport' => 'Public Transportation',
                ]
            ],
            'education' => [
                'label' => 'Education (Éducation)',
                'subcategories' => [
                    'universities' => 'Universities & Higher Education',
                    'schools' => 'Primary & Secondary Schools',
                    'training' => 'Training & Professional Development',
                    'edtech' => 'EdTech',
                ]
            ],
            'media' => [
                'label' => 'Media & Entertainment (Médias)',
                'subcategories' => [
                    'television' => 'Television & Broadcasting',
                    'film' => 'Film & Production',
                    'gaming' => 'Gaming & Esports',
                    'publishing' => 'Publishing',
                    'music' => 'Music & Audio',
                ]
            ],
            'hospitality' => [
                'label' => 'Hospitality & Tourism (Hospitality)',
                'subcategories' => [
                    'hotels' => 'Hotels & Accommodations',
                    'restaurants' => 'Restaurants & Food Service',
                    'travel' => 'Travel & Tourism',
                    'casinos' => 'Casinos & Gaming',
                ]
            ],
            'nonprofit' => [
                'label' => 'Non-Profit Organizations (ONG)',
                'subcategories' => [
                    'humanitarian' => 'Humanitarian & Relief',
                    'development' => 'Development & Education NGO',
                    'environmental' => 'Environmental Conservation',
                    'advocacy' => 'Advocacy & Human Rights',
                    'charity' => 'Charity & Community Services',
                ]
            ],
            'government' => [
                'label' => 'Government & Public Administration (Gouvernement)',
                'subcategories' => [
                    'federal' => 'Federal Government',
                    'state' => 'State/Provincial Government',
                    'local' => 'Local Government',
                    'public_service' => 'Public Service',
                ]
            ],
            'professional_services' => [
                'label' => 'Professional Services (Services Professionnels)',
                'subcategories' => [
                    'consulting' => 'Consulting',
                    'legal' => 'Legal Services',
                    'accounting' => 'Accounting & Auditing',
                    'hr_recruitment' => 'HR & Recruitment',
                    'marketing' => 'Marketing & Advertising',
                ]
            ],
            'agriculture' => [
                'label' => 'Agriculture & Food Production (Agriculture)',
                'subcategories' => [
                    'farming' => 'Farming & Crop Production',
                    'livestock' => 'Livestock & Ranching',
                    'aquaculture' => 'Aquaculture',
                    'food_processing' => 'Food Processing',
                ]
            ],
            'telecommunications' => [
                'label' => 'Telecommunications (Télécommunications)',
                'subcategories' => [
                    'mobile' => 'Mobile Operators',
                    'broadband' => 'Broadband & Internet',
                    'satellites' => 'Satellite Communications',
                ]
            ],
            'utilities' => [
                'label' => 'Utilities & Water (Utilités)',
                'subcategories' => [
                    'water' => 'Water & Wastewater',
                    'electricity' => 'Electricity Distribution',
                    'gas' => 'Gas Distribution',
                ]
            ],
        ];
    }

    /**
     * Get all sectors as flat array
     */
    public static function getFlatList(): array
    {
        $flat = [];
        foreach (self::getAll() as $key => $sector) {
            $flat[$key] = $sector['label'];
        }
        return $flat;
    }

    /**
     * Get subcategories for a sector
     */
    public static function getSubcategories(string $sector): array
    {
        $all = self::getAll();
        return $all[$sector]['subcategories'] ?? [];
    }
}
