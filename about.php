<?php
require_once __DIR__ . '/includes/db.php';
if (file_exists(__DIR__ . '/includes/seo.php')) { require_once __DIR__ . '/includes/seo.php'; }

$pageSeo = getSeoData('static', null, 'about');
require_once __DIR__ . '/includes/header.php';
?>

<!-- Hero Banner Section -->
<section class="py-5 text-white position-relative" style="background: linear-gradient(135deg, #0458A0 0%, #033663 100%);">
    <div class="container py-4">
        <?= generateBreadcrumbs(['Home' => 'index.php', 'About Us' => 'about.php']) ?>
        <div class="row align-items-center mt-3">
            <div class="col-lg-8">
                <span class="badge bg-warning text-dark px-3 py-2 rounded-pill fw-bold text-uppercase mb-3">
                    <i class="fa-solid fa-award me-1"></i> Est. 2070 B.S. • MoEST Approved • TITI Certified
                </span>
                <h1 class="fw-bold display-5 text-white mb-3">About Vision Point Educational Service</h1>
                <p class="lead text-white-50 mb-4" style="line-height: 1.7;">
                    Empowering Nepalese students to achieve their academic and professional ambitions through transparent admissions counseling, verified university partnerships, and dedicated scholarship facilitation.
                </p>
                <div class="d-flex gap-3 flex-wrap">
                    <button class="btn btn-warning text-dark fw-bold px-4 py-2 rounded-pill shadow-sm" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">
                        <i class="fa-solid fa-user-graduate me-2"></i>Book Free Consultation
                    </button>
                    <a href="<?= h($basePath) ?>contact.php" class="btn btn-outline-light px-4 py-2 rounded-pill fw-semibold">
                        <i class="fa-solid fa-location-dot me-2"></i>Visit Biratnagar Office
                    </a>
                </div>
            </div>
            <div class="col-lg-4 d-none d-lg-block text-center">
                <div class="p-4 bg-white bg-opacity-10 rounded-4 border border-white border-opacity-25 shadow-lg backdrop-blur">
                    <i class="fa-solid fa-award text-warning display-3 mb-3"></i>
                    <h5 class="fw-bold text-white mb-2">Student First Approach</h5>
                    <p class="small text-white-50 mb-0">Committed to 100% transparent fee structures, direct university affiliations, and zero hidden processing charges.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission, Vision & Core Values -->
<section class="py-5 bg-light">
    <div class="container py-3">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="card h-100 p-4 border-0 shadow-sm rounded-4">
                    <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 54px; height: 54px;">
                        <i class="fa-solid fa-compass fs-4"></i>
                    </div>
                    <h3 class="fw-bold text-primary fs-5 mb-2">Our Mission</h3>
                    <p class="text-muted small mb-0" style="line-height: 1.7;">
                        To provide accurate, unbiased, and comprehensive academic counseling to Nepalese students, ensuring seamless admissions into accredited institutions in India and overseas with maximum scholarship support.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 p-4 border-0 shadow-sm rounded-4">
                    <div class="bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 54px; height: 54px;">
                        <i class="fa-solid fa-eye fs-4 text-warning"></i>
                    </div>
                    <h3 class="fw-bold text-primary fs-5 mb-2">Our Vision</h3>
                    <p class="text-muted small mb-0" style="line-height: 1.7;">
                        To be recognized as the most reliable, transparent, and student-centric educational advisory bureau in eastern Nepal, setting industry benchmarks for ethical counseling and documentation integrity.
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card h-100 p-4 border-0 shadow-sm rounded-4">
                    <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center mb-3" style="width: 54px; height: 54px;">
                        <i class="fa-solid fa-handshake-angle fs-4 text-success"></i>
                    </div>
                    <h3 class="fw-bold text-primary fs-5 mb-2">Our Values</h3>
                    <p class="text-muted small mb-0" style="line-height: 1.7;">
                        Integrity, transparency, student empathy, and relentless commitment to genuine guidance without commercial bias. We guide based on aptitude, merit, and career suitability.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Who We Are Section -->
<section class="py-5">
    <div class="container py-3">
        <div class="row align-items-center g-5">
            <div class="col-lg-6">
                <span class="text-danger fw-bold small text-uppercase letter-spacing-1">About Our Foundation</span>
                <h2 class="fw-bold text-primary display-6 mb-3">Dedicated to Transforming Dreams into Academic Reality</h2>
                <p class="text-muted" style="line-height: 1.8;">
                    <strong>Vision Point Educational Service (VPES)</strong> was established in <strong>2070 B.S. (वि.सं. २०७०)</strong> in Biratnagar, Nepal. Operating under the approval of the <strong>Ministry of Education, Science and Technology (MoEST)</strong> with <strong>TITI (Training Institute for Technical Instruction)</strong> certified counselors, we bring over 13 years of steadfast dedication to higher education advisory. We specialize in providing end-to-end guidance for higher education in recognized universities across India (Delhi NCR, Bangalore, Chennai, Punjab, Gujarat) as well as destinations like Australia, UK, USA, and Canada.
                </p>
                <p class="text-muted" style="line-height: 1.8;">
                    Choosing the right course and college is one of the most critical decisions a student makes. Our team of experienced counselors evaluates each student's academic background (+2, Diploma, or Bachelor's), personal interests, and financial budget to match them with accredited universities recognized by regulatory bodies like UGC, AICTE, INC, NMC, and PCI.
                </p>
                <ul class="list-unstyled text-muted small mb-4">
                    <li class="mb-2 d-flex align-items-center"><i class="fa-solid fa-circle-check text-success me-2"></i> Certified counselors with deep knowledge of Indian & global education systems.</li>
                    <li class="mb-2 d-flex align-items-center"><i class="fa-solid fa-circle-check text-success me-2"></i> Direct tie-ups with 80+ top universities and colleges across India.</li>
                    <li class="mb-2 d-flex align-items-center"><i class="fa-solid fa-circle-check text-success me-2"></i> Guidance on merit and bilateral scholarships for Nepalese citizens.</li>
                    <li class="mb-2 d-flex align-items-center"><i class="fa-solid fa-circle-check text-success me-2"></i> Assistance with hostel booking, local guardian setup, and travel orientation.</li>
                </ul>
            </div>
            <div class="col-lg-6">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="p-4 bg-primary text-white rounded-4 text-center shadow">
                            <h3 class="display-5 fw-bold mb-1">80+</h3>
                            <p class="small mb-0 text-white-50">Partner Universities</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-4 bg-warning text-dark rounded-4 text-center shadow">
                            <h3 class="display-5 fw-bold mb-1">100%</h3>
                            <p class="small mb-0 text-dark-50 fw-semibold">Transparent Fees</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-4 bg-light border rounded-4 text-center shadow-sm">
                            <h3 class="display-5 fw-bold text-primary mb-1">18+</h3>
                            <p class="small mb-0 text-muted">Course Streams</p>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-4 bg-danger text-white rounded-4 text-center shadow">
                            <h3 class="display-5 fw-bold mb-1">2070</h3>
                            <p class="small mb-0 text-white-50">Established (B.S.)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Leadership Perspectives: Chairman Say & Director Message -->
<section class="py-5 bg-white border-top border-bottom">
    <div class="container py-3">
        <div class="text-center max-w-800 mx-auto mb-5">
            <span class="badge bg-primary-subtle text-primary px-3 py-1.5 rounded-pill fw-bold text-uppercase mb-2">
                <i class="fa-solid fa-user-tie me-1"></i> Executive Leadership
            </span>
            <h2 class="fw-bold text-primary display-6">Voices of Leadership & Commitment</h2>
            <p class="text-muted small">Guiding Nepali students with integrity, MoEST compliance, and TITI-certified professional excellence since 2070 B.S.</p>
        </div>

        <!-- 1. Chairman Say (अध्यक्षको सन्देश) -->
        <div class="card border-0 rounded-4 shadow-sm bg-light p-4 p-lg-5 mb-5">
            <div class="row align-items-center g-5">
                <div class="col-lg-4 text-center">
                    <div class="position-relative d-inline-block">
                        <div class="rounded-4 overflow-hidden shadow-md border border-3 border-white mx-auto" style="width: 240px; height: 280px;">
                            <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=600&auto=format&fit=crop&q=80" alt="Chairman - Vision Point Educational Service" class="w-100 h-100 object-fit-cover">
                        </div>
                        <div class="position-absolute bottom-0 start-50 translate-middle-x mb-n2 bg-danger text-white py-1.5 px-3 rounded-pill shadow-sm small fw-bold text-nowrap">
                            <i class="fa-solid fa-crown text-warning me-1"></i> Chairman Say
                        </div>
                    </div>
                    <h5 class="fw-bold text-dark mt-4 mb-1">Chairman's Perspective</h5>
                    <p class="text-muted extra-small mb-0">Vision Point Educational Service (Est. 2070 B.S.)</p>
                </div>
                <div class="col-lg-8">
                    <span class="text-danger fw-bold small text-uppercase letter-spacing-1">Chairman Say • अध्यक्षको भनाइ</span>
                    <h3 class="fw-bold text-primary fs-3 mt-1 mb-3">Fostering Academic Ambitions with Uncompromising Integrity Since 2070 B.S.</h3>
                    <div class="p-3 bg-white rounded-3 border-start border-4 border-danger shadow-sm mb-3">
                        <p class="fst-italic text-dark mb-0 small" style="line-height: 1.8;">
                            "When we founded Vision Point Educational Service in 2070 B.S. in Biratnagar, our core commitment was simple yet uncompromising: to provide honest, transparent, and student-centered educational guidance without commercial bias. Over the past 13+ years, that founding philosophy has remained our greatest strength."
                        </p>
                    </div>
                    <p class="text-muted small" style="line-height: 1.8;">
                        Every student who steps into our office carries the heartfelt dreams of their family and the aspiration of a transformative career. As Chairman, I guarantee that our advisory is anchored in strict ethical codes, verified approvals from the <strong>Ministry of Education, Science and Technology (MoEST)</strong>, and certified counseling credentials from <strong>TITI (Training Institute for Technical Instruction)</strong>.
                    </p>
                    <p class="text-muted small mb-0" style="line-height: 1.8;">
                        We do not merely process admission forms; we build life-long bridges of trust between Nepalese scholars and world-class universities across India, Australia, the UK, the USA, and Canada. We warmly welcome students and guardians to experience the Vision Point standard of educational advisory.
                    </p>
                </div>
            </div>
        </div>

        <!-- 2. Director Message (प्रबन्ध निर्देशकको सन्देश) -->
        <div class="card border-0 rounded-4 shadow-sm bg-light p-4 p-lg-5">
            <div class="row align-items-center g-5 flex-lg-row-reverse">
                <div class="col-lg-4 text-center">
                    <div class="position-relative d-inline-block">
                        <div class="rounded-4 overflow-hidden shadow-md border border-3 border-white mx-auto" style="width: 240px; height: 280px;">
                            <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?w=600&auto=format&fit=crop&q=80" alt="Managing Director - Vision Point Educational Service" class="w-100 h-100 object-fit-cover">
                        </div>
                        <div class="position-absolute bottom-0 start-50 translate-middle-x mb-n2 bg-primary text-white py-1.5 px-3 rounded-pill shadow-sm small fw-bold text-nowrap">
                            <i class="fa-solid fa-certificate text-warning me-1"></i> Director Message
                        </div>
                    </div>
                    <h5 class="fw-bold text-dark mt-4 mb-1">Managing Director</h5>
                    <p class="text-muted extra-small mb-0">Vision Point Educational Service (Est. 2070 B.S.)</p>
                </div>
                <div class="col-lg-8">
                    <span class="text-primary fw-bold small text-uppercase letter-spacing-1">Director Message • निर्देशकको सन्देश</span>
                    <h3 class="fw-bold text-primary fs-3 mt-1 mb-3">Empowering Nepalese Scholars on the Global Stage with Professional Excellence</h3>
                    <div class="p-3 bg-white rounded-3 border-start border-4 border-primary shadow-sm mb-3">
                        <p class="fst-italic text-dark mb-0 small" style="line-height: 1.8;">
                            "Higher education is a life-defining investment. At Vision Point Educational Service, our operational team works tirelessly to bridge the gap between ambitious students in Nepal and accredited, top-tier global universities with 100% transparent fee structures."
                        </p>
                    </div>
                    <p class="text-muted small" style="line-height: 1.8;">
                        Our senior counselors are formally trained and certified by <strong>TITI (Training Institute for Technical Instruction, Sanothimi)</strong>, ensuring that aptitude profiling, course recommendations, and financial documentation adhere to national and international benchmarks. Operating under the official recognition of the <strong>Ministry of Education, Science and Technology (MoEST)</strong>, we hold direct representation agreements with 80+ accredited universities.
                    </p>
                    <div class="row g-2 pt-2">
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-circle-check text-success small"></i>
                                <span class="fw-semibold text-dark extra-small">Established in 2070 B.S. (13+ Yrs of Trust)</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-circle-check text-success small"></i>
                                <span class="fw-semibold text-dark extra-small">MoEST Approved Educational Agency</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-circle-check text-success small"></i>
                                <span class="fw-semibold text-dark extra-small">TITI Certified Education Counselors</span>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="d-flex align-items-center gap-2">
                                <i class="fa-solid fa-circle-check text-success small"></i>
                                <span class="fw-semibold text-dark extra-small">Council Recognition (NMC/NNC/NEC/PCI)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Why Choose Us Grid -->
<section class="py-5 bg-light">
    <div class="container py-3">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="text-danger fw-bold small text-uppercase">Our Distinct Advantages</span>
            <h2 class="fw-bold text-primary display-6">Why Students & Parents Trust Vision Point</h2>
            <p class="text-muted small">We prioritize honest advisory over sales targets, ensuring every student makes a well-informed academic decision.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-4">
                <div class="card p-4 h-100 border-0 shadow-sm rounded-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="p-3 bg-primary-subtle text-primary rounded-3"><i class="fa-solid fa-file-signature fs-4"></i></div>
                        <h4 class="fw-bold text-dark fs-6 mb-0">Official Admission Letters</h4>
                    </div>
                    <p class="text-muted small mb-0">Direct provisional offer letters and confirmation directly from university admissions directorates with zero middlemen ambiguity.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card p-4 h-100 border-0 shadow-sm rounded-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="p-3 bg-warning-subtle text-warning rounded-3"><i class="fa-solid fa-hand-holding-dollar fs-4 text-warning"></i></div>
                        <h4 class="fw-bold text-dark fs-6 mb-0">Scholarship Advocacy</h4>
                    </div>
                    <p class="text-muted small mb-0">We assist eligible students in applying for merit-based fee waivers and institutional concessions of up to 50%-100% tuition coverage.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card p-4 h-100 border-0 shadow-sm rounded-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="p-3 bg-success-subtle text-success rounded-3"><i class="fa-solid fa-building-columns fs-4 text-success"></i></div>
                        <h4 class="fw-bold text-dark fs-6 mb-0">Council Approvals Verification</h4>
                    </div>
                    <p class="text-muted small mb-0">We only recommend colleges whose programs are recognized by Nepal councils: Nepal Medical Council (NMC), Nursing Council (NNC), and Engineering Council (NEC).</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card p-4 h-100 border-0 shadow-sm rounded-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="p-3 bg-info-subtle text-info rounded-3"><i class="fa-solid fa-bed fs-4 text-info"></i></div>
                        <h4 class="fw-bold text-dark fs-6 mb-0">Hostel & Accommodation</h4>
                    </div>
                    <p class="text-muted small mb-0">Assistance with on-campus hygienic hostels (separate for boys & girls) with verified Nepalese/North/South Indian food options.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card p-4 h-100 border-0 shadow-sm rounded-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="p-3 bg-danger-subtle text-danger rounded-3"><i class="fa-solid fa-passport fs-4 text-danger"></i></div>
                        <h4 class="fw-bold text-dark fs-6 mb-0">Border & Travel Protocols</h4>
                    </div>
                    <p class="text-muted small mb-0">Detailed briefings on student documentation required for Indian border transit (Passport/Citizenship, College Letter) and overseas visa filing.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-4">
                <div class="card p-4 h-100 border-0 shadow-sm rounded-4">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="p-3 bg-secondary-subtle text-secondary rounded-3"><i class="fa-solid fa-phone-volume fs-4 text-secondary"></i></div>
                        <h4 class="fw-bold text-dark fs-6 mb-0">Continuous Guardian Support</h4>
                    </div>
                    <p class="text-muted small mb-0">Our support doesn't end at admission. We remain a point of contact for parents throughout the student's study tenure.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our Dedicated Advisory Team -->
<section class="py-5 bg-white">
    <div class="container py-3">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="text-danger fw-bold small text-uppercase">Counseling Excellence</span>
            <h2 class="fw-bold text-primary display-6">Meet Our Expert Advisory Team</h2>
            <p class="text-muted small">Experienced educationists, certified visa specialists, and student mentors dedicated to your academic success.</p>
        </div>

        <div class="row g-4">
            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden text-center p-3 team-card">
                    <div class="rounded-4 overflow-hidden mb-3 mx-auto" style="width: 140px; height: 140px;">
                        <img src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?w=300&auto=format&fit=crop&q=80" alt="Senior Admissions Counselor" class="w-100 h-100 object-fit-cover">
                    </div>
                    <h5 class="fw-bold text-primary fs-6 mb-1">Roshni Bhattarai</h5>
                    <span class="badge bg-primary-subtle text-primary rounded-pill mb-2 px-3 py-1 small">Head of Career Counseling</span>
                    <p class="text-muted extra-small mb-3">8+ years of expertise in medical, engineering, and nursing admissions in India. Specialist in scholarship matching.</p>
                    <div class="d-flex justify-content-center gap-2 mt-auto">
                        <a href="tel:+9779807765812" class="btn btn-sm btn-outline-primary rounded-circle" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"><i class="fa-solid fa-phone"></i></a>
                        <a href="mailto:info@visionpoint.edu.np" class="btn btn-sm btn-outline-primary rounded-circle" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"><i class="fa-solid fa-envelope"></i></a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden text-center p-3 team-card">
                    <div class="rounded-4 overflow-hidden mb-3 mx-auto" style="width: 140px; height: 140px;">
                        <img src="https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=300&auto=format&fit=crop&q=80" alt="Study Abroad Director" class="w-100 h-100 object-fit-cover">
                    </div>
                    <h5 class="fw-bold text-primary fs-6 mb-1">Suresh K. Nepal</h5>
                    <span class="badge bg-warning-subtle text-dark rounded-pill mb-2 px-3 py-1 small">Overseas Visa Specialist</span>
                    <p class="text-muted extra-small mb-3">Certified Australian & UK Education Agent. Expert in Genuine Student (GS) compliance, GIC filing, and visa documentation.</p>
                    <div class="d-flex justify-content-center gap-2 mt-auto">
                        <a href="tel:+9779807765812" class="btn btn-sm btn-outline-primary rounded-circle" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"><i class="fa-solid fa-phone"></i></a>
                        <a href="mailto:info@visionpoint.edu.np" class="btn btn-sm btn-outline-primary rounded-circle" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"><i class="fa-solid fa-envelope"></i></a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden text-center p-3 team-card">
                    <div class="rounded-4 overflow-hidden mb-3 mx-auto" style="width: 140px; height: 140px;">
                        <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?w=300&auto=format&fit=crop&q=80" alt="Healthcare Admissions Lead" class="w-100 h-100 object-fit-cover">
                    </div>
                    <h5 class="fw-bold text-primary fs-6 mb-1">Pooja Regmi</h5>
                    <span class="badge bg-success-subtle text-success rounded-pill mb-2 px-3 py-1 small">Medical & Health Sciences Lead</span>
                    <p class="text-muted extra-small mb-3">Specialist in B.Sc Nursing, B.Pharm, and MBBS placements with INC/NMC council equivalence auditing.</p>
                    <div class="d-flex justify-content-center gap-2 mt-auto">
                        <a href="tel:+9779807765812" class="btn btn-sm btn-outline-primary rounded-circle" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"><i class="fa-solid fa-phone"></i></a>
                        <a href="mailto:info@visionpoint.edu.np" class="btn btn-sm btn-outline-primary rounded-circle" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"><i class="fa-solid fa-envelope"></i></a>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="card h-100 border-0 shadow-sm rounded-4 overflow-hidden text-center p-3 team-card">
                    <div class="rounded-4 overflow-hidden mb-3 mx-auto" style="width: 140px; height: 140px;">
                        <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?w=300&auto=format&fit=crop&q=80" alt="Test Prep Coordinator" class="w-100 h-100 object-fit-cover">
                    </div>
                    <h5 class="fw-bold text-primary fs-6 mb-1">Amit Shrestha</h5>
                    <span class="badge bg-info-subtle text-info rounded-pill mb-2 px-3 py-1 small">Test Prep & Language Mentor</span>
                    <p class="text-muted extra-small mb-3">Certified British Council and Pearson trainer with 7+ years of experience helping students achieve 7.5+ in IELTS and 65+ in PTE.</p>
                    <div class="d-flex justify-content-center gap-2 mt-auto">
                        <a href="tel:+9779807765812" class="btn btn-sm btn-outline-primary rounded-circle" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"><i class="fa-solid fa-phone"></i></a>
                        <a href="mailto:info@visionpoint.edu.np" class="btn btn-sm btn-outline-primary rounded-circle" style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;"><i class="fa-solid fa-envelope"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Accreditations & Statutory Standing: MoEST, TITI, Est. 2070 -->
<section class="py-5 bg-light border-top">
    <div class="container py-3">
        <div class="text-center max-w-700 mx-auto mb-5">
            <span class="text-danger fw-bold small text-uppercase">Statutory Standing</span>
            <h2 class="fw-bold text-primary display-6">Accreditations & Government Approvals</h2>
            <p class="text-muted small">Vision Point Educational Service operates in complete accordance with the laws, regulations, and guidelines of the Government of Nepal.</p>
        </div>

        <div class="row g-4">
            <!-- 1. Ministry of Education, Science & Technology (MoEST) -->
            <div class="col-md-6 col-lg-3">
                <div class="card p-4 h-100 border-0 shadow-sm rounded-4 bg-white text-center">
                    <div class="bg-primary-subtle text-primary rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 56px; height: 56px;">
                        <i class="fa-solid fa-landmark fs-4"></i>
                    </div>
                    <span class="badge bg-danger text-white rounded-pill px-2.5 py-1 extra-small mb-2 align-self-center">Government Approved</span>
                    <h5 class="fw-bold text-primary fs-6 mb-2">Ministry of Education (MoEST)</h5>
                    <p class="text-muted extra-small mb-0" style="line-height: 1.6;">
                        Approved by the Ministry of Education, Science & Technology (शिक्षा, विज्ञान तथा प्रविधि मन्त्रालय), Government of Nepal for certified educational consultancy and student counseling.
                    </p>
                </div>
            </div>

            <!-- 2. TITI Certified Counselors -->
            <div class="col-md-6 col-lg-3">
                <div class="card p-4 h-100 border-0 shadow-sm rounded-4 bg-white text-center">
                    <div class="bg-warning-subtle text-warning rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 56px; height: 56px;">
                        <i class="fa-solid fa-user-graduate fs-4 text-warning"></i>
                    </div>
                    <span class="badge bg-warning text-dark rounded-pill px-2.5 py-1 extra-small mb-2 align-self-center">TITI Certified</span>
                    <h5 class="fw-bold text-primary fs-6 mb-2">TITI Certified Counselors</h5>
                    <p class="text-muted extra-small mb-0" style="line-height: 1.6;">
                        Senior education counselors formally trained and certified by <strong>TITI (Training Institute for Technical Instruction, Sanothimi)</strong>, CTEVT, ensuring ethical and standardized guidance.
                    </p>
                </div>
            </div>

            <!-- 3. Established 2070 B.S. -->
            <div class="col-md-6 col-lg-3">
                <div class="card p-4 h-100 border-0 shadow-sm rounded-4 bg-white text-center">
                    <div class="bg-success-subtle text-success rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 56px; height: 56px;">
                        <i class="fa-solid fa-calendar-check fs-4 text-success"></i>
                    </div>
                    <span class="badge bg-success text-white rounded-pill px-2.5 py-1 extra-small mb-2 align-self-center">Est. 2070 B.S.</span>
                    <h5 class="fw-bold text-primary fs-6 mb-2">13+ Years of Heritage</h5>
                    <p class="text-muted extra-small mb-0" style="line-height: 1.6;">
                        Established in <strong>2070 B.S.</strong> in Biratnagar. Registered under Company Registrar's Office and Inland Revenue Department (PAN/VAT) with full financial and corporate transparency.
                    </p>
                </div>
            </div>

            <!-- 4. Direct Council Verified Degrees -->
            <div class="col-md-6 col-lg-3">
                <div class="card p-4 h-100 border-0 shadow-sm rounded-4 bg-white text-center">
                    <div class="bg-info-subtle text-info rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 56px; height: 56px;">
                        <i class="fa-solid fa-shield-halved fs-4 text-info"></i>
                    </div>
                    <span class="badge bg-info text-white rounded-pill px-2.5 py-1 extra-small mb-2 align-self-center">Council Verified</span>
                    <h5 class="fw-bold text-primary fs-6 mb-2">Council Recognitions</h5>
                    <p class="text-muted extra-small mb-0" style="line-height: 1.6;">
                        Direct university representation ensuring 100% equivalence and license eligibility from Nepal Medical (NMC), Nursing (NNC), Engineering (NEC), and Pharmacy (NPC) councils.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action -->
<section class="py-5 text-center text-white" style="background: #0458A0;">
    <div class="container py-3">
        <h2 class="fw-bold text-white display-6 mb-3">Ready to Discuss Your Academic Future?</h2>
        <p class="text-white-50 max-w-600 mx-auto mb-4">Book a 1-on-1 counseling session with our experienced education advisors at our Biratnagar office or online.</p>
        <div class="d-flex justify-content-center gap-3 flex-wrap">
            <button class="btn btn-warning text-dark fw-bold px-4 py-2 rounded-pill shadow" data-bs-toggle="modal" data-bs-target="#scholarshipFormModal">
                <i class="fa-solid fa-paper-plane me-2"></i>Apply for Free Counseling
            </button>
            <a href="tel:+9779807765812" class="btn btn-outline-light px-4 py-2 rounded-pill fw-semibold">
                <i class="fa-solid fa-phone me-2"></i>Call +977 9807765812
            </a>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

