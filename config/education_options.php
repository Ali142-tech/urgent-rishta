<?php

/*
 * The education choices shown in the signup form, the "Complete your profile" form and the dashboard, in the order the client
 * gave them (a name that appears twice in the client's list is shown once). Each name is a masterdata EDUCATION row — see the
 * add_client_education_options migration; members who already have an older education keep it.
 *
 * The last entry is the "other" choice: the member types their own and it is saved as its own EDUCATION row
 * (App\Services\EducationOptions::resolve()).
 */
return [
    'other' => 'Other (Please Specify)',

    'list' => [
        // School / secondary
        'Primary School', 'Middle School', 'Matriculation / SSC', 'O Levels / IGCSE / GCSE', 'Intermediate / HSSC', 'FA',
        'FSc (Pre-Medical)', 'FSc (Pre-Engineering)', 'ICS', 'I.Com', 'A Levels', 'High School Diploma (USA/Canada)', 'GED',
        'International Baccalaureate (IB)', 'Higher Secondary Certificate (HSC)', 'BTEC / Vocational Certificate',
        'Other Secondary Qualification',
        // Certificates & diplomas
        'Certificate', 'Professional Certificate', 'Diploma', 'DAE (Associate Engineering)', 'Higher National Certificate (HNC)',
        'Higher National Diploma (HND)', 'Advanced Diploma', 'Associate Degree (AA / AS / AAS)', 'Foundation Degree',
        'Technical / Vocational Qualification', 'Apprenticeship Qualification', 'Other Diploma',
        // Bachelor's
        'BA', 'BSc / BS', 'BCom', 'BBA', 'BEng / BE', 'BTech', 'BCA', 'BEd', 'LLB', 'BArch', 'BDes', 'BFA', 'BPharm / PharmD',
        'BSN / BSc Nursing', 'BSCS', 'BS Software Engineering', 'BS Information Technology', 'BS Data Science',
        'BS Artificial Intelligence', 'BS Cybersecurity', 'BS Psychology', 'BS Biotechnology', 'BS Agriculture', 'BS Economics',
        'BS Accounting & Finance', 'BDS', 'MBBS', 'MD (Primary Medical Degree)', 'DVM', 'DPT', 'Bachelor of Social Work (BSW)',
        'Bachelor of Public Health', 'Bachelor of Aviation', 'Bachelor of Hospitality Management', "Other Bachelor's Degree",
        // Master's
        'MA', 'MSc / MS', 'MCom', 'MBA', 'Executive MBA (EMBA)', 'MEng / ME', 'MTech', 'MCA', 'MEd', 'LLM', 'MArch', 'MDes', 'MFA',
        'MPH', 'MPA', 'MPP', 'MPhil', 'MRes', 'MSc Computer Science', 'MS Data Science', 'MS Artificial Intelligence',
        'MS Cybersecurity', 'MS Software Engineering', 'MS Finance', 'MS Accounting', 'MS Psychology',
        'Master of Social Work (MSW)', 'Master of Nursing (MSN)', 'Master of Public Health', "Other Master's Degree",
        // Doctorates
        'PhD', 'DPhil', 'DBA', 'EdD', 'EngD', 'DSc / ScD', 'Doctor of Psychology (PsyD)', 'Doctor of Public Health (DrPH)',
        'Doctor of Nursing Practice (DNP)', 'Doctor of Law (SJD / JSD)', 'Postdoctoral Research', 'Other Doctoral Degree',
        // Medical
        'MD', 'DO (Doctor of Osteopathic Medicine)', 'DDS', 'DMD', 'PharmD', 'FCPS', 'MCPS', 'MRCP', 'FRCP', 'MRCS', 'FRCS', 'FRCR',
        'MRCOG', 'FRCOG', 'USMLE', 'PLAB', 'AMC', 'Nursing Diploma / RN', 'BSN', 'MSN', 'NP / Advanced Practice Nursing',
        'Physician Assistant (PA)', 'Allied Health Qualification', 'Medical Residency', 'Medical Fellowship',
        'Other Medical Qualification',
        // Professional
        'CA (Chartered Accountant)', 'ACCA', 'ACA (ICAEW)', 'CPA', 'CMA', 'CIMA', 'CFA', 'FRM', 'CIA', 'CISA', 'CISSP', 'PMP',
        'PRINCE2', 'SHRM', 'CIPD', 'CIPP', 'Actuarial Qualification (ACAS / FCAS / FIA)', 'Solicitor Qualification (SQE / LPC)',
        'Bar-at-Law / Barrister', 'Legal Practice Qualification', 'Chartered Engineer (CEng)', 'Professional Engineer (PE / PEng)',
        'RIBA / Chartered Architect', 'Other Professional Qualification',
        // Religious
        'Hifz-ul-Quran', 'Qirat / Tajweed', 'Dars-e-Nizami', 'Alim / Alima', 'Fazil / Fazila', 'Shahadat-ul-Almiya', 'Mufti / Ifta',
        'Islamic Studies Diploma', 'BA Islamic Studies', 'MA Islamic Studies', 'PhD Islamic Studies', 'Other Religious Qualification',
        // Studying
        'Currently Studying', 'Undergraduate Student', 'Postgraduate Student', 'Professional Qualification in Progress',
        'Incomplete Degree',
    ],
];
