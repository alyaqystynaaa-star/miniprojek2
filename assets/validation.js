function showClientError(form, message) {
    const box = form.querySelector('[data-client-message]');
    if (!box) {
        return;
    }

    box.textContent = message;
    box.className = 'message error';
    box.style.display = 'block';
}

function clearClientError(form) {
    const box = form.querySelector('[data-client-message]');
    if (!box) {
        return;
    }

    box.textContent = '';
    box.style.display = 'none';
}

document.addEventListener('DOMContentLoaded', () => {
    const registerForm = document.querySelector('[data-validate="register"]');
    if (registerForm) {
        registerForm.addEventListener('submit', (event) => {
            clearClientError(registerForm);

            const name = registerForm.querySelector('[name="name"]').value.trim();
            const email = registerForm.querySelector('[name="email"]').value.trim();
            const password = registerForm.querySelector('[name="password"]').value;

            if (name.length < 3) {
                event.preventDefault();
                showClientError(registerForm, 'Name must be at least 3 characters.');
                return;
            }

            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                event.preventDefault();
                showClientError(registerForm, 'Please enter a valid email address.');
                return;
            }

            if (password.length < 8) {
                event.preventDefault();
                showClientError(registerForm, 'Password must be at least 6 characters.');
            }
        });
    }

    const loginForm = document.querySelector('[data-validate="login"]');
    if (loginForm) {
        loginForm.addEventListener('submit', (event) => {
            clearClientError(loginForm);

            const email = loginForm.querySelector('[name="email"]').value.trim();
            const password = loginForm.querySelector('[name="password"]').value;

            if (!email || !password) {
                event.preventDefault();
                showClientError(loginForm, 'Email and password are required.');
                return;
            }

            if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                event.preventDefault();
                showClientError(loginForm, 'Please enter a valid email address.');
            }
        });
    }

    const assignmentForm = document.querySelector('[data-validate="assignment"]');
    if (assignmentForm) {
        assignmentForm.addEventListener('submit', (event) => {
            clearClientError(assignmentForm);

            const title = assignmentForm.querySelector('[name="title"]').value.trim();
            const description = assignmentForm.querySelector('[name="description"]').value.trim();

            if (title.length < 3) {
                event.preventDefault();
                showClientError(assignmentForm, 'Assignment title must be at least 3 characters.');
                return;
            }

            if (description.length < 10) {
                event.preventDefault();
                showClientError(assignmentForm, 'Description must be at least 10 characters.');
            }
        });
    }

    const submissionForm = document.querySelector('[data-validate="submission"]');
    if (submissionForm) {
        submissionForm.addEventListener('submit', (event) => {
            clearClientError(submissionForm);

            const assignment = submissionForm.querySelector('[name="assignment_id"]').value;
            const fileInput = submissionForm.querySelector('[name="submission_file"]');
            const allowed = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'txt', 'zip'];

            if (!assignment) {
                event.preventDefault();
                showClientError(submissionForm, 'Please choose an assignment.');
                return;
            }

            if (!fileInput.files.length) {
                event.preventDefault();
                showClientError(submissionForm, 'Please choose a file to upload.');
                return;
            }

            const file = fileInput.files[0];
            const parts = file.name.split('.');
            const extension = parts.length > 1 ? parts.pop().toLowerCase() : '';

            if (!allowed.includes(extension)) {
                event.preventDefault();
                showClientError(submissionForm, 'Allowed file types: pdf, doc, docx, ppt, pptx, txt, zip.');
                return;
            }

            if (file.size <= 0) {
                event.preventDefault();
                showClientError(submissionForm, 'Uploaded file cannot be empty.');
                return;
            }

            if (file.size > 5 * 1024 * 1024) {
                event.preventDefault();
                showClientError(submissionForm, 'File size must not exceed 5MB.');
            }
        });
    }
});
