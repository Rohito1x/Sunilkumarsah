<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/header.php';

// Get target country code
$code = isset($_GET['code']) ? strtoupper(trim((string)$_GET['code'])) : 'USA';

// Support both US and USA slug matching
if ($code === 'US') {
    $code = 'USA';
}
if ($code === 'CAN' || $code === 'CANADA') {
    $code = 'CA';
}
if ($code === 'AUS' || $code === 'AUSTRALIA') {
    $code = 'AU';
}

// Country rich static database mock map
$countriesData = [
    'USA' => [
        'name' => 'United States of America',
        'short_name' => 'US',
        'banner' => 'https://images.unsplash.com/photo-1501594907352-04cda38ebc29?w=1200&auto=format&fit=crop&q=80',
        'about' => 'One of the most appealing aspects of the U.S. higher education system is its remarkable flexibility, offering students an array of choices through a diverse range of institutions. This diversity allows students to pursue specialized academic disciplines and even acquire practical skills for the workforce.<br><br>
                    In the U.S., higher education is comprised of nearly 4,000 accredited institutions, each unique in its offerings. Unlike many other countries, the U.S. doesn\'t centralize its higher education system under one authority. Instead, institutions are accredited either nationally or regionally by independent bodies, ensuring quality across the board.<br><br>
                    Students can choose from various types of institutions to match their academic goals. For instance, liberal arts colleges provide a broad education in the arts, humanities, languages, and social and physical sciences. Most of these institutions are private, relying on a mix of endowments, alumni donations, research grants, and tuition fees. These schools are typically smaller than public universities, and some have religious affiliations or cater exclusively to one gender.<br><br>
                    Another path is through community colleges, which offer two-year associate degree programs. These programs are designed either to prepare students for further undergraduate study or to equip them with the skills needed for immediate entry into the workforce. On the other hand, state colleges and universities, commonly referred to as "public universities," were established by U.S. state governments to provide affordable education to state residents. These institutions often boast extensive research opportunities and a wide variety of courses. They are generally larger and more inclusive in their admissions than private universities.<br><br>
                    In the U.S., regardless of the type of institution, students earn credits for the courses they complete, which count towards their degree. Degree programs typically include "core" courses to build a strong academic foundation, "major" courses to focus on a specific subject area, and "electives" to allow exploration of other interests, ensuring a well-rounded education.<br><br>
                    The academic year in the U.S. generally runs from September to May and is usually split into two semesters of 16-18 weeks. However, some schools operate on a quarter or trimester system, with terms lasting 10-12 weeks.<br><br>
                    With such a variety of higher education options available, students in the U.S. are sure to find the perfect fit for their academic ambitions, financial circumstances, and personal preferences.',
        'colleges' => [
            ['name' => 'University of South Florida', 'logo' => 'https://www.usf.edu/favicon.ico', 'website' => 'https://www.usf.edu/', 'location' => '4202 E Fowler Ave, Tampa, FL 33620, USA', 'photo' => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=600&auto=format&fit=crop&q=60'],
            ['name' => 'Arizona State University', 'logo' => 'https://www.asu.edu/favicon.ico', 'website' => 'https://www.asu.edu/', 'location' => 'Tempe, AZ 85281, United States', 'photo' => 'https://images.unsplash.com/photo-1562774053-701939374585?w=600&auto=format&fit=crop&q=60'],
            ['name' => 'George Mason University', 'logo' => 'https://www.gmu.edu/favicon.ico', 'website' => 'https://www.gmu.edu/', 'location' => '4400 University Dr, Fairfax, VA 22030, USA', 'photo' => 'https://images.unsplash.com/photo-1592280771190-3e2e4d571952?w=600&auto=format&fit=crop&q=60'],
            ['name' => 'University of North Texas', 'logo' => 'https://www.unt.edu/favicon.ico', 'website' => 'https://www.unt.edu/', 'location' => '1155 Union Cir, Denton, TX 76203, USA', 'photo' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=600&auto=format&fit=crop&q=60'],
            ['name' => 'Western Kentucky University', 'logo' => 'https://www.wku.edu/favicon.ico', 'website' => 'https://www.wku.edu/', 'location' => '1906 College Heights Blvd, Bowling Green, KY 42101', 'photo' => 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?w=600&auto=format&fit=crop&q=60'],
            ['name' => 'Wichita State University', 'logo' => 'https://www.wichita.edu/favicon.ico', 'website' => 'https://www.wichita.edu/', 'location' => '1845 Fairmount St, Wichita, KS 67260, USA', 'photo' => 'https://images.unsplash.com/photo-1519452635265-7b1fbfd1e4e0?w=600&auto=format&fit=crop&q=60']
        ],
        'requirements' => [
            'personal' => [
                'Full Name (as per Passport)',
                'Email Address & Phone Number',
                'Permanent & Current Address in Nepal',
                'Emergency Contact Details of Parents/Guardians'
            ],
            'academic' => [
                'School Leaving Certificate (SEE) Marksheet & Character Certificate',
                'Higher Secondary (+2 / CBSE) Transcript & Migration Certificate',
                'Bachelor Degree Transcript & Provisional (for Master applicants)',
                'Statement of Purpose (SOP) tailored to course',
                '2 Letters of Recommendation (LOR) from teachers/professors',
                'Valid Passport (minimum 6 months validity)',
                'English Proficiency: IELTS 6.0+ / PTE 54+ / Duolingo 105+ / TOEFL 80+'
            ],
            'visa' => [
                'Official I-20 Form from US University',
                'DS-160 Confirmation Page',
                'SEVIS I-901 Fee Payment Receipt ($350)',
                'MRV Visa Application Fee Payment Receipt ($185)',
                'Bank Balance Certificate & Statement showing 1-year tuition + living',
                'Source of Income & Property Valuation Certificates'
            ]
        ]
    ],
    'UK' => [
        'name' => 'United Kingdom',
        'short_name' => 'UK',
        'banner' => 'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?w=1200&auto=format&fit=crop&q=80',
        'about' => 'The United Kingdom is home to some of the world\'s oldest and most prestigious universities, offering world-class qualifications recognized by employers and academics worldwide. Studying in the UK provides an opportunity to experience a rich cultural history while receiving top-tier training.<br><br>
                    UK degree programs are highly focused and generally shorter than in other countries. Bachelor\'s degrees are typically completed in three years, and Master\'s degrees take only one year of intensive study. This accelerated structure helps students save on tuition and accommodation costs, graduating and entering the job market much faster.<br><br>
                    The UK government has also introduced the Graduate Route visa, allowing international students who have successfully completed an undergraduate or master\'s degree to stay and work, or look for work, for two years after graduation. Doctoral graduates can stay for three years, opening up outstanding career development prospects.<br><br>
                    Whether you choose historical institutions in Oxford and Cambridge or modern, cutting-edge research facilities in London, Manchester, and Edinburgh, the UK offers an unparalleled academic experience.',
        'colleges' => [
            ['name' => 'BPP University', 'logo' => 'https://www.bpp.com/favicon.ico', 'website' => 'https://www.bpp.com/', 'location' => 'BPP House, Aldine Place, 142-144 Uxbridge Road, London, W12 8AA, UK', 'photo' => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=600&auto=format&fit=crop&q=60'],
            ['name' => 'University of East London', 'logo' => 'https://www.uel.ac.uk/favicon.ico', 'website' => 'https://www.uel.ac.uk/', 'location' => 'University of East London, University Way, London, E16 2RD, United Kingdom', 'photo' => 'https://images.unsplash.com/photo-1592280771190-3e2e4d571952?w=600&auto=format&fit=crop&q=60'],
            ['name' => 'University of Roehampton', 'logo' => 'https://www.roehampton.ac.uk/favicon.ico', 'website' => 'https://www.roehampton.ac.uk/', 'location' => 'University of Roehampton, Grove House, Roehampton Lane, London, SW15 5PJ, UK', 'photo' => 'https://images.unsplash.com/photo-1562774053-701939374585?w=600&auto=format&fit=crop&q=60'],
            ['name' => 'University of the West of Scotland', 'logo' => 'https://www.uws.ac.uk/favicon.ico', 'website' => 'https://www.uws.ac.uk/', 'location' => 'Hamilton International Technology Park, Stephenson Pl, Blantyre, Glasgow G72 0LH, UK', 'photo' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=600&auto=format&fit=crop&q=60'],
            ['name' => 'Ulster University', 'logo' => 'https://www.ulster.ac.uk/favicon.ico', 'website' => 'https://www.ulster.ac.uk/', 'location' => 'St James\' House, 10 Rosebery Avenue, Holborn, London EC1R 4TF', 'photo' => 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?w=600&auto=format&fit=crop&q=60'],
            ['name' => 'Northumbria University', 'logo' => 'https://www.northumbria.ac.uk/favicon.ico', 'website' => 'https://www.northumbria.ac.uk/', 'location' => 'Ellison Pl, Newcastle upon Tyne NE1 8ST, United Kingdom', 'photo' => 'https://images.unsplash.com/photo-1527891751199-7225231a68dd?w=600&auto=format&fit=crop&q=60'],
            ['name' => 'Ravensbourne University London', 'logo' => 'https://www.ravensbourne.ac.uk/favicon.ico', 'website' => 'https://www.ravensbourne.ac.uk/', 'location' => 'Plot No. 2, Yamuna Expy, opposite Buddha International Circuit, Sector 17A, Greater Noida, Uttar Pradesh 203201, India', 'photo' => 'https://images.unsplash.com/photo-1503676260728-1c00da094a0b?w=600&auto=format&fit=crop&q=60'],
            ['name' => 'Coventry University', 'logo' => 'https://www.coventry.ac.uk/favicon.ico', 'website' => 'https://www.coventry.ac.uk/', 'location' => 'University House, 109 - 117 Middlesex Street, London E1 7JF', 'photo' => 'https://images.unsplash.com/photo-1519452635265-7b1fbfd1e4e0?w=600&auto=format&fit=crop&q=60'],
            ['name' => 'University of Sunderland', 'logo' => 'https://www.sunderland.ac.uk/favicon.ico', 'website' => 'https://www.sunderland.ac.uk/', 'location' => 'Edinburgh Building, City Campus, Chester Road, Sunderland SR1 3SD', 'photo' => 'https://images.unsplash.com/photo-1504805572947-34fad45aed93?w=600&auto=format&fit=crop&q=60'],
            ['name' => 'University of West London', 'logo' => 'https://www.uwl.ac.uk/favicon.ico', 'website' => 'https://www.uwl.ac.uk/', 'location' => 'St Mary\'s Rd, London W5 5RF, United Kingdom', 'photo' => 'https://images.unsplash.com/photo-1498243691581-b145c3f54a5c?w=600&auto=format&fit=crop&q=60'],
            ['name' => 'University of Chester', 'logo' => 'https://www.chester.ac.uk/favicon.ico', 'website' => 'https://www.chester.ac.uk/', 'location' => 'Parkgate Rd, Chester CH1 4BJ, UK', 'photo' => 'https://images.unsplash.com/photo-1517486808906-6ca8b3f04846?w=600&auto=format&fit=crop&q=60'],
            ['name' => 'University of Greenwich', 'logo' => 'https://www.gre.ac.uk/favicon.ico', 'website' => 'https://www.gre.ac.uk/', 'location' => 'Old Royal Naval College, Park Row, Greenwich, London SE10 9LS', 'photo' => 'https://images.unsplash.com/photo-1492538368577-8b5fd6ce0f77?w=600&auto=format&fit=crop&q=60'],
            ['name' => 'London Metropolitan University', 'logo' => 'https://www.londonmet.ac.uk/favicon.ico', 'website' => 'https://www.londonmet.ac.uk/', 'location' => '166-220 Holloway Rd, London N7 8DB, United Kingdom', 'photo' => 'https://images.unsplash.com/photo-1607013407627-6ee814329547?w=600&auto=format&fit=crop&q=60']
        ],
        'requirements' => [
            'personal' => [
                'Full Name',
                'Email Address',
                'Contact Details (Full Address)',
                'Emergency Contact Number of family'
            ],
            'academic' => [
                'School Leaving Certificate (SEE)',
                'SEE Marksheet',
                'SEE Character Certificate',
                'Higher Secondary Transcript',
                'Higher Secondary Migration Certificate',
                'Higher Secondary Provisional Certificate',
                'Higher Secondary Character Certificate (or all Previous academic documents)',
                'Citizenship ID (Back and Front)',
                'Passport-size Photograph',
                '11th Grade Marksheet',
                'Letter of Recommendation (LOR) & Medium of Instruction (MOI)',
                'IELTS / PTE / DUOLINGO scores (as per college norms)'
            ],
            'noc' => [
                'Passport-size Photograph',
                'Citizenship ID / Passport (Back and Front)',
                'Previous All Academic Documents',
                'Signature on plain paper',
                'Admission Offer Letter from college/university',
                'College Invoice Letter'
            ],
            'medical' => [
                'CEE Entrance Username and Password',
                'CEE Exam Admit Card and Confirmation Page'
            ],
            'travel' => [
                'Passport (Back and Front)',
                'Email Address',
                'Contact Details'
            ]
        ]
    ],
    'CA' => [
        'name' => 'Canada',
        'short_name' => 'Canada',
        'banner' => 'https://images.unsplash.com/photo-1507608869274-d3177c8bb4c7?w=1200&auto=format&fit=crop&q=80',
        'about' => 'Canada is recognized globally for its high academic standards, safe campus environments, and highly welcoming immigration policies. Canadian universities and colleges rank among the best in the world, offering programs that integrate academic learning with practical cooperative education (Co-op) programs.<br><br>
                    One of Canada\'s biggest highlights is the Post-Graduation Work Permit (PGWP) program. International graduates from eligible designated learning institutions (DLIs) can obtain open work permits for up to three years, providing them with valuable Canadian work experience that counts towards permanent residency pathways.<br><br>
                    Additionally, living in Canada offers a premium quality of life with clean natural spaces, vibrant multicultural cities, and lower average tuition costs compared to neighboring destinations. Canada is consistently ranked by the United Nations as one of the best countries in the world to live and study.',
        'colleges' => [
            ['name' => 'Humber College', 'logo' => 'https://humber.ca/favicon.ico', 'website' => 'https://humber.ca/', 'location' => '205 Humber College Blvd, Toronto, ON M9W 5L7, Canada', 'photo' => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=600&auto=format&fit=crop&q=60'],
            ['name' => 'Seneca Polytechnic', 'logo' => 'https://www.senecapolytechnic.ca/favicon.ico', 'website' => 'https://www.senecapolytechnic.ca/', 'location' => '1750 Finch Ave E, North York, ON M2J 2X5, Canada', 'photo' => 'https://images.unsplash.com/photo-1562774053-701939374585?w=600&auto=format&fit=crop&q=60'],
            ['name' => 'Fanshawe College', 'logo' => 'https://www.fanshawec.ca/favicon.ico', 'website' => 'https://www.fanshawec.ca/', 'location' => '1001 Fanshawe College Blvd, London, ON N5Y 5R6, Canada', 'photo' => 'https://images.unsplash.com/photo-1592280771190-3e2e4d571952?w=600&auto=format&fit=crop&q=60'],
            ['name' => 'Conestoga College', 'logo' => 'https://www.conestogac.on.ca/favicon.ico', 'website' => 'https://www.conestogac.on.ca/', 'location' => '299 Doon Valley Dr, Kitchener, ON N2G 4M4, Canada', 'photo' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=600&auto=format&fit=crop&q=60'],
            ['name' => 'Langara College', 'logo' => 'https://langara.ca/favicon.ico', 'website' => 'https://langara.ca/', 'location' => '100 W 49th Ave, Vancouver, BC V5Y 2Z6, Canada', 'photo' => 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?w=600&auto=format&fit=crop&q=60'],
            ['name' => 'Cape Breton University', 'logo' => 'https://www.cbu.ca/favicon.ico', 'website' => 'https://www.cbu.ca/', 'location' => '1250 Grand Lake Rd, Sydney, NS B1P 6L2, Canada', 'photo' => 'https://images.unsplash.com/photo-1519452635265-7b1fbfd1e4e0?w=600&auto=format&fit=crop&q=60']
        ],
        'requirements' => [
            'personal' => [
                'Full Name (as per Passport)',
                'Email Address & Contact Information',
                'Government Issued Citizenship Certificate',
                'Emergency Contacts of Parents/Guardian'
            ],
            'academic' => [
                'SEE Marksheet & Character Certificate',
                '+2 / Intermediate Transcript & Migration Certificate',
                'Bachelor Degree Transcript & Degree Certificate (for PGWP programs)',
                'Statement of Purpose (SOP / Study Plan)',
                'Letters of Recommendation & Resume/CV',
                'English Test: IELTS 6.0 (no band < 6.0) or PTE 60+ (for SDS)'
            ],
            'visa' => [
                'Letter of Acceptance (LOA) from DLI-approved Canadian institution',
                'Provincial Attestation Letter (PAL) where applicable',
                'Guaranteed Investment Certificate (GIC) of CAD 20,635',
                'Full 1st Year Tuition Fee Payment Receipt',
                'Upfront Medical Examination by Panel Physician in Nepal',
                'Police Clearance Certificate (PCC) from Nepal Police'
            ]
        ]
    ],
    'AU' => [
        'name' => 'Australia',
        'short_name' => 'Australia',
        'banner' => 'https://images.unsplash.com/photo-1523482580672-f109ba8cb9be?w=1200&auto=format&fit=crop&q=80',
        'about' => 'Australia offers a diverse and vibrant learning destination, featuring highly respected institutions, modern laboratory infrastructures, and laid-back coastal lifestyles. Australian qualifications are highly sought after by global recruiters due to the country\'s practical and industry-aligned teaching methodologies.<br><br>
                    The Australian education system is heavily regulated by the government through the Education Services for Overseas Students (ESOS) framework, ensuring that international students receive tuition protection and quality courses.<br><br>
                    Australia offers generous post-study work rights, allowing international graduates to stay, work, and gain professional experience in their field. Combine this with competitive student support frameworks and some of the sunniest cities in the world, and it is easy to see why Australia remains a top study abroad choice.',
        'colleges' => [
            ['name' => 'Macquarie University', 'logo' => 'https://www.mq.edu.au/favicon.ico', 'website' => 'https://www.mq.edu.au/', 'location' => 'Wallumattagal Campus, Macquarie Park NSW 2109, Australia', 'photo' => 'https://images.unsplash.com/photo-1541339907198-e08756dedf3f?w=600&auto=format&fit=crop&q=60'],
            ['name' => 'Deakin University', 'logo' => 'https://www.deakin.edu.au/favicon.ico', 'website' => 'https://www.deakin.edu.au/', 'location' => '221 Burwood Hwy, Burwood VIC 3125, Australia', 'photo' => 'https://images.unsplash.com/photo-1562774053-701939374585?w=600&auto=format&fit=crop&q=60'],
            ['name' => 'University of Wollongong', 'logo' => 'https://www.uow.edu.au/favicon.ico', 'website' => 'https://www.uow.edu.au/', 'location' => 'Northfields Ave, Wollongong NSW 2522, Australia', 'photo' => 'https://images.unsplash.com/photo-1592280771190-3e2e4d571952?w=600&auto=format&fit=crop&q=60'],
            ['name' => 'Western Sydney University', 'logo' => 'https://www.westernsydney.edu.au/favicon.ico', 'website' => 'https://www.westernsydney.edu.au/', 'location' => 'Locked Bag 1797, Penrith NSW 2751, Australia', 'photo' => 'https://images.unsplash.com/photo-1523050854058-8df90110c9f1?w=600&auto=format&fit=crop&q=60'],
            ['name' => 'Griffith University', 'logo' => 'https://www.griffith.edu.au/favicon.ico', 'website' => 'https://www.griffith.edu.au/', 'location' => '170 Kessels Rd, Nathan QLD 4111, Australia', 'photo' => 'https://images.unsplash.com/photo-1507679799987-c73779587ccf?w=600&auto=format&fit=crop&q=60'],
            ['name' => 'Edith Cowan University', 'logo' => 'https://www.ecu.edu.au/favicon.ico', 'website' => 'https://www.ecu.edu.au/', 'location' => '270 Joondalup Dr, Joondalup WA 6027, Australia', 'photo' => 'https://images.unsplash.com/photo-1519452635265-7b1fbfd1e4e0?w=600&auto=format&fit=crop&q=60']
        ],
        'requirements' => [
            'personal' => [
                'Full Name as shown in Machine Readable Passport',
                'Email Address & Active Mobile Number',
                'Permanent Address & Family Relationship Verification Certificate',
                'Citizenship Certificates of Student & Financial Sponsors'
            ],
            'academic' => [
                'SEE Marksheet & Character Certificate',
                '+2 / Intermediate Transcript, Migration & Character Certificates',
                'Bachelor Degree Transcripts & Degree Certificate (for Master)',
                'Genuine Student (GS) Statement addressing career trajectory',
                'English Test: IELTS 6.0+ (no band < 5.5) or PTE 50+ (undergrad) / IELTS 6.5+ (master)',
                'Letter of Recommendation & Medium of Instruction certificate'
            ],
            'visa' => [
                'Confirmation of Enrolment (CoE) from CRICOS registered provider',
                'Overseas Student Health Cover (OSHC) policy receipt',
                'Evidence of 1-Year Living Costs (AUD 29,710) + 1-Year Tuition + Return Airfare',
                'Approved Education Loan Sanction Letter from Class A Commercial Bank',
                'Income Verification Certificates, Tax Clearance & Property Valuation',
                'Biometrics appointment at VFS Global in Kathmandu'
            ]
        ]
    ]
];

// Fallback to USA if country code not recognized
if (!array_key_exists($code, $countriesData)) {
    $code = 'USA';
}

$country = $countriesData[$code];
?>

<!-- Breadcrumbs Header Section -->
<section class="py-5 text-white position-relative" style="background: linear-gradient(rgba(16, 46, 72, 0.85), rgba(16, 46, 72, 0.85)), url('<?= h($country['banner']) ?>') no-repeat center center / cover; min-height: 220px; display: flex; align-items: center;">
    <div class="container py-3">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2 fw-medium small">
                <li class="breadcrumb-item"><a href="<?= h($basePath) ?>index.php" class="text-decoration-none text-white-50"><i class="fa fa-house me-1"></i>Home</a></li>
                <li class="breadcrumb-item"><a href="<?= h($basePath) ?>study-abroad.php" class="text-decoration-none text-white-50">Study Abroad</a></li>
                <li class="breadcrumb-item active text-white" aria-current="page"><?= h($country['short_name']) ?></li>
            </ol>
        </nav>
        <h1 class="fw-bold text-white mb-0 display-5 position-relative d-inline-block">
            Study in <?= h($country['short_name']) ?>
            <span class="position-absolute start-0 bottom-0 bg-warning rounded-pill" style="height: 4px; width: 60px; margin-bottom: -8px;"></span>
        </h1>
    </div>
</section>

<!-- Detail Section -->
<section class="py-5 bg-white">
    <div class="container">
        <div class="row g-5">
            <!-- Left description -->
            <div class="col-lg-7">
                <article class="paragraph-module text-muted" style="line-height: 1.8;">
                    <h2 class="section-title fw-bold text-primary mb-4">About <span class="text-warning"><?= h($country['name']) ?></span></h2>
                    <div>
                        <?= $country['about'] ?>
                    </div>
                </article>

                <?php if (!empty($country['colleges'])): ?>
                    <!-- UK Top Brand Colleges -->
                    <div class="mt-5 animate-fade-in">
                        <h3 class="fw-bold text-primary mb-4 position-relative d-inline-block" style="font-size: 22px;">
                            <i class="fa-solid fa-graduation-cap text-warning me-2"></i>Top Partner Universities & Colleges in <?= h($country['short_name']) ?>
                            <span class="position-absolute start-0 bottom-0 bg-warning rounded-pill" style="height: 3px; width: 50px; margin-bottom: -5px;"></span>
                        </h3>
                        <div class="row g-3 mt-2">
                            <?php foreach ($country['colleges'] as $col): ?>
                                <div class="col-12 col-md-6">
                                    <div class="card uk-college-card h-100 border border-light shadow-sm d-flex flex-column justify-content-between rounded-4 overflow-hidden bg-white">
                                        <div class="position-relative overflow-hidden" style="height: 140px;">
                                            <img src="<?= h($col['photo']) ?>" class="w-100 h-100 object-fit-cover" alt="Campus Photo">
                                            <div class="position-absolute bottom-0 start-0 m-2 bg-white rounded-circle p-1 border shadow-sm d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; z-index: 2;">
                                                <img src="<?= h($col['logo']) ?>" class="object-fit-contain w-100 h-100" onerror="this.onerror=null; this.src='https://cdn-icons-png.flaticon.com/512 - 8074/8074788.png';" alt="College logo">
                                            </div>
                                        </div>
                                        <div class="p-3 d-flex flex-column justify-content-between flex-grow-1">
                                            <div class="mb-3">
                                                <h6 class="fw-bold text-primary mb-2" style="font-size: 14px; line-height: 1.4;"><?= h($col['name']) ?></h6>
                                                <div class="text-muted small d-flex align-items-start gap-1" style="font-size: 11px; line-height: 1.3;">
                                                    <i class="fa-solid fa-location-dot text-warning mt-0.5" style="flex-shrink: 0;"></i>
                                                    <span><?= h($col['location']) ?></span>
                                                </div>
                                            </div>
                                            <a href="<?= h($col['website']) ?>" target="_blank" class="btn btn-outline-secondary btn-sm w-100 rounded-pill py-2 fw-semibold d-inline-flex align-items-center justify-content-center gap-1" style="font-size: 11px;">
                                                <span>Visit Website</span>
                                                <i class="fa-solid fa-arrow-up-right-from-square" style="font-size: 9px;"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <!-- Information Required Checklist Section -->
                    <div class="mt-5 animate-fade-in">
                        <h3 class="fw-bold text-primary mb-4 position-relative d-inline-block" style="font-size: 22px;">
                            <i class="fa-solid fa-file-circle-check text-warning me-2"></i>Information Required to Study in UK
                            <span class="position-absolute start-0 bottom-0 bg-warning rounded-pill" style="height: 3px; width: 50px; margin-bottom: -5px;"></span>
                        </h3>
                        
                        <div class="accordion accordion-flush shadow-sm rounded-4 overflow-hidden border border-light mt-3" id="ukRequirementsAccordion" style="border-radius: 12px;">
                            <!-- Personal Info -->
                            <div class="accordion-item border-bottom border-light">
                                <h2 class="accordion-header" id="headingPersonal">
                                    <button class="accordion-button fw-bold text-primary py-3 bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePersonal" aria-expanded="true" aria-controls="collapsePersonal">
                                        <i class="fa-solid fa-user text-warning me-2"></i>1. Personal Information
                                    </button>
                                </h2>
                                <div id="collapsePersonal" class="accordion-collapse collapse show" aria-labelledby="headingPersonal" data-bs-parent="#ukRequirementsAccordion">
                                    <div class="accordion-body bg-white pt-1">
                                        <ul class="list-group list-group-flush bg-transparent">
                                            <?php foreach ($country['requirements']['personal'] as $req): ?>
                                                <li class="list-group-item bg-transparent border-0 ps-0 text-muted small d-flex align-items-center gap-2 py-2">
                                                    <i class="fa-solid fa-circle-check text-success"></i> <?= h($req) ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Academic Documents -->
                            <div class="accordion-item border-bottom border-light">
                                <h2 class="accordion-header" id="headingAcademic">
                                    <button class="accordion-button collapsed fw-bold text-primary py-3 bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseAcademic" aria-expanded="false" aria-controls="collapseAcademic">
                                        <i class="fa-solid fa-file-invoice text-warning me-2"></i>2. Documents Required
                                    </button>
                                </h2>
                                <div id="collapseAcademic" class="accordion-collapse collapse" aria-labelledby="headingAcademic" data-bs-parent="#ukRequirementsAccordion">
                                    <div class="accordion-body bg-white pt-1">
                                        <ul class="list-group list-group-flush bg-transparent">
                                            <?php foreach ($country['requirements']['academic'] as $req): ?>
                                                <li class="list-group-item bg-transparent border-0 ps-0 text-muted small d-flex align-items-start gap-2 py-2">
                                                    <i class="fa-solid fa-circle-check text-success mt-1"></i> <span><?= h($req) ?></span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- NOC Requirements -->
                            <div class="accordion-item border-bottom border-light">
                                <h2 class="accordion-header" id="headingNOC">
                                    <button class="accordion-button collapsed fw-bold text-primary py-3 bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNOC" aria-expanded="false" aria-controls="collapseNOC">
                                        <i class="fa-solid fa-stamp text-warning me-2"></i>3. Requirements For NOC (No Objection Certificate)
                                    </button>
                                </h2>
                                <div id="collapseNOC" class="accordion-collapse collapse" aria-labelledby="headingNOC" data-bs-parent="#ukRequirementsAccordion">
                                    <div class="accordion-body bg-white pt-1">
                                        <ul class="list-group list-group-flush bg-transparent">
                                            <?php foreach ($country['requirements']['noc'] as $req): ?>
                                                <li class="list-group-item bg-transparent border-0 ps-0 text-muted small d-flex align-items-start gap-2 py-2">
                                                    <i class="fa-solid fa-circle-check text-success mt-1"></i> <span><?= h($req) ?></span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Medical Courses -->
                            <div class="accordion-item border-bottom border-light">
                                <h2 class="accordion-header" id="headingMedical">
                                    <button class="accordion-button collapsed fw-bold text-primary py-3 bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseMedical" aria-expanded="false" aria-controls="collapseMedical">
                                        <i class="fa-solid fa-briefcase-medical text-warning me-2"></i>4. For Bachelor's Medical Courses
                                    </button>
                                </h2>
                                <div id="collapseMedical" class="accordion-collapse collapse" aria-labelledby="headingMedical" data-bs-parent="#ukRequirementsAccordion">
                                    <div class="accordion-body bg-white pt-1">
                                        <ul class="list-group list-group-flush bg-transparent">
                                            <?php foreach ($country['requirements']['medical'] as $req): ?>
                                                <li class="list-group-item bg-transparent border-0 ps-0 text-muted small d-flex align-items-start gap-2 py-2">
                                                    <i class="fa-solid fa-circle-check text-success mt-1"></i> <span><?= h($req) ?></span>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Travel Tickets -->
                            <div class="accordion-item border-bottom-0">
                                <h2 class="accordion-header" id="headingTravel">
                                    <button class="accordion-button collapsed fw-bold text-primary py-3 bg-white" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTravel" aria-expanded="false" aria-controls="collapseTravel">
                                        <i class="fa-solid fa-plane-departure text-warning me-2"></i>5. Requirements For Travel Tickets
                                    </button>
                                </h2>
                                <div id="collapseTravel" class="accordion-collapse collapse" aria-labelledby="headingTravel" data-bs-parent="#ukRequirementsAccordion">
                                    <div class="accordion-body bg-white pt-1">
                                        <ul class="list-group list-group-flush bg-transparent">
                                            <?php foreach ($country['requirements']['travel'] as $req): ?>
                                                <li class="list-group-item bg-transparent border-0 ps-0 text-muted small d-flex align-items-center gap-2 py-2">
                                                    <i class="fa-solid fa-circle-check text-success"></i> <?= h($req) ?>
                                                </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right Sidebar -->
            <div class="col-lg-4 offset-lg-1">
                <!-- Have Any Question Widget -->
                <div class="card border border-light rounded-4 shadow-sm p-4 bg-primary text-white mb-4">
                    <h5 class="fw-bold mb-3 text-white">Have Any Question?</h5>
                    <ul class="list-unstyled mb-0 d-flex flex-column gap-3">
                        <li class="d-flex align-items-start gap-3">
                            <i class="fa-solid fa-phone text-warning mt-1 fs-5"></i>
                            <div>
                                <div class="small text-white-50">Call Counselor</div>
                                <a href="tel:<?= htmlspecialchars($siteSettings['contact_phone'] ?? '+977 9807765812') ?>" class="text-white text-decoration-none fw-semibold"><?= htmlspecialchars($siteSettings['contact_phone'] ?? '+977 9807765812') ?></a>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3">
                            <i class="fa-solid fa-location-dot text-warning mt-1 fs-5"></i>
                            <div>
                                <div class="small text-white-50">Office Location</div>
                                <span class="small"><?= htmlspecialchars($siteSettings['contact_location'] ?? 'CRT Building, Putalisadak, Kathmandu') ?></span>
                            </div>
                        </li>
                        <li class="d-flex align-items-start gap-3">
                            <i class="fa-solid fa-envelope text-warning mt-1 fs-5"></i>
                            <div>
                                <div class="small text-white-50">Email Support</div>
                                <a href="mailto:<?= htmlspecialchars($siteSettings['contact_email'] ?? 'info@visionpoint.edu.np') ?>" class="text-white text-decoration-none small"><?= htmlspecialchars($siteSettings['contact_email'] ?? 'info@visionpoint.edu.np') ?></a>
                            </div>
                        </li>
                    </ul>
                    
                    <hr class="border-white-50 my-3">
                    
                    <h6 class="fw-semibold text-white mb-2" style="font-size: 14px;">Follow us on:</h6>
                    <div class="d-flex gap-2">
                        <a href="https://facebook.com" target="_blank" class="text-white"><i class="fa-brands fa-facebook fs-5"></i></a>
                        <a href="https://instagram.com" target="_blank" class="text-white"><i class="fa-brands fa-instagram fs-5"></i></a>
                        <a href="https://twitter.com" target="_blank" class="text-white"><i class="fa-brands fa-twitter fs-5"></i></a>
                    </div>
                </div>

                <!-- Scholarship Promo CTA Widget -->
                <div class="card border-0 rounded-4 shadow-sm text-white overflow-hidden mb-4" style="background: linear-gradient(135deg, #e56920 0%, #d25a17 100%);">
                    <div style="height: 180px; position: relative;">
                        <img src="<?= h($basePath) ?>assets/images/student_hero.png?v=<?= filemtime(__DIR__ . '/assets/images/student_hero.png') ?>" class="w-100 h-100 object-fit-cover" style="object-position: center 20%;" alt="Student Scholarship">
                    </div>
                    <div class="p-4 text-center">
                        <h4 class="fw-bold mb-2">Start your Scholarship</h4>
                        <p class="small text-white-50 mb-4">Apply for Scholarships today to fund your academic pursuits in <?= h($country['short_name']) ?>.</p>
                        <button class="btn btn-light w-100 rounded-pill py-3 fw-bold text-danger hover-scale" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal" data-country="<?= h($country['short_name']) ?>">
                            Apply Now
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Bottom Orange Newsletter Section -->
<section class="py-5 text-white" style="background-color: #e56920; position: relative; overflow: hidden;">
    <div class="container py-3 text-center position-relative" style="z-index: 5;">
        <h2 class="fw-bold text-white mb-2">Subscribe to our Newsletter!</h2>
        <p class="text-white-50 mb-4 max-w-600 mx-auto small">Stay informed about the latest admission cycles, visa eligibility guidelines, and scholarships abroad.</p>
        
        <form id="subscribe_form_abroad" class="max-w-500 mx-auto bg-white p-1 rounded-pill d-flex align-items-center shadow-sm">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">
            <input type="email" name="email" placeholder="Enter your email address" class="form-control border-0 bg-transparent flex-grow-1 px-4" style="box-shadow:none; font-size:14px;" required>
            <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-semibold text-white">Subscribe</button>
        </form>
    </div>
</section>

<?php
require_once __DIR__ . '/includes/footer.php';
?>
