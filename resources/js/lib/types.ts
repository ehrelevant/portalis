export enum Status {
    Returned = 'Returned',
    None = 'None',
    ForReview = 'For Review',
    Accepted = 'Accepted',
}

interface SubmissionIdStatus {
    requirement_id: number;
    status: Status;
}

interface FormIdStatus {
    form_id: number;
    status: Status;
}

export interface StudentProps {
    user_id: number;
    student_id: number;
    student_number: string;

    first_name: string;
    middle_name?: string;
    last_name: string;

    email: string;
    wordpress_name: string;
    wordpress_email: string;

    has_dropped: boolean;
    is_disabled: boolean;

    // Foreign information
    faculty_id?: number;
    section?: string;

    supervisor_id?: number;
    supervisor_first_name?: string;
    supervisor_last_name?: string;

    company_id?: number;
    company_name?: string;

    // Statuses
    form_id_statuses: FormIdStatus[];
    submission_id_statuses: SubmissionIdStatus[];
}

export interface Requirement {
    requirement_id: number;
    requirement_name: string;
    deadline: Date;
}

export interface Faculty {
    faculty_id: number;
    section?: string;
}

export interface Company {
    company_id: number;
    company_name: string;
    is_disabled: boolean;
}

export interface SupervisorCompanyIdName {
    supervisor_id: number;
    company_id?: number;
    first_name: string;
    middle_name?: string;
    last_name: string;
}

export interface FormIdName {
    form_id: number;
    form_name: string;
    short_name: string;
}

export interface SupervisorProps {
    user_id: number;
    supervisor_id: number;

    first_name: string;
    middle_name?: string;
    last_name: string;

    email: string;

    is_disabled: boolean;

    company_id?: number;
    company_name?: string;

    form_id_statuses: FormIdStatus[];
}

export interface FacultyProps {
    user_id: number;
    faculty_id: number;

    first_name: string;
    middle_name?: string;
    last_name: string;

    email: string;
    section?: string;

    is_disabled: boolean;
}
