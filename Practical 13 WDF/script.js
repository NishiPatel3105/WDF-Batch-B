const apiURL = "api.php";

const form = document.getElementById("studentForm");
const studentList = document.getElementById("studentList");
const message = document.getElementById("message");
const loading = document.getElementById("loading");
const studentId = document.getElementById("studentId");
const saveButton = document.getElementById("saveButton");
const cancelButton = document.getElementById("cancelButton");

async function requestAPI(method, data = null) {
    const options = { method };

    if (data !== null) {
        options.headers = {
            "Content-Type": "application/json"
        };
        options.body = JSON.stringify(data);
    }

    const response = await fetch(apiURL, options);
    const result = await response.json();

    if (!response.ok || !result.success) {
        throw new Error(result.message || "Request failed");
    }

    return result;
}

async function loadStudents() {
    loading.hidden = false;
    message.textContent = "";

    try {
        const result = await requestAPI("GET");
        studentList.replaceChildren();

        result.data.forEach(student => {
            const row = document.createElement("tr");

            [
                student.id,
                student.name,
                student.email,
                student.course
            ].forEach(value => {
                const cell = document.createElement("td");
                cell.textContent = value;
                row.appendChild(cell);
            });

            const actions = document.createElement("td");

            const editButton = document.createElement("button");
            editButton.textContent = "Edit";
            editButton.addEventListener("click", () => editStudent(student));

            const deleteButton = document.createElement("button");
            deleteButton.textContent = "Delete";
            deleteButton.addEventListener("click", () => deleteStudent(student.id));

            actions.append(editButton, deleteButton);
            row.appendChild(actions);
            studentList.appendChild(row);
        });

        if (result.data.length === 0) {
            studentList.textContent = "No students found.";
        }
    } catch (error) {
        message.textContent = error.message;
    } finally {
        loading.hidden = true;
    }
}

form.addEventListener("submit", async event => {
    event.preventDefault();

    const data = {
        name: document.getElementById("name").value.trim(),
        email: document.getElementById("email").value.trim(),
        course: document.getElementById("course").value.trim()
    };

    const editing = studentId.value !== "";

    if (editing) {
        data.id = Number(studentId.value);
    }

    saveButton.disabled = true;
    message.textContent = "Saving...";

    try {
        const result = await requestAPI(editing ? "PUT" : "POST", data);

        message.textContent = result.message;
        form.reset();
        studentId.value = "";
        saveButton.textContent = "Add Student";
        cancelButton.hidden = true;

        await loadStudents();
        message.textContent = result.message;
    } catch (error) {
        message.textContent = error.message;
    } finally {
        saveButton.disabled = false;
    }
});

function editStudent(student) {
    studentId.value = student.id;
    document.getElementById("name").value = student.name;
    document.getElementById("email").value = student.email;
    document.getElementById("course").value = student.course;

    saveButton.textContent = "Update Student";
    cancelButton.hidden = false;

    form.scrollIntoView({ behavior: "smooth" });
}

cancelButton.addEventListener("click", () => {
    form.reset();
    studentId.value = "";
    saveButton.textContent = "Add Student";
    cancelButton.hidden = true;
    message.textContent = "";
});

async function deleteStudent(id) {
    if (!confirm("Are you sure you want to delete this student?")) {
        return;
    }

    message.textContent = "Deleting...";

    try {
        const result = await requestAPI("DELETE", { id });
        await loadStudents();
        message.textContent = result.message;
    } catch (error) {
        message.textContent = error.message;
    }
}

loadStudents();

