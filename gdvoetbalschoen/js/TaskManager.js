const Buttons = document.querySelectorAll(".Edit");
const UpdateTask = document.getElementById("UpdateTask");
const CloseField = document.getElementById("CloseField");
const DeleteTaskButton = document.getElementById("DeleteTask");

const EditBlock = document.getElementById("EditBlock");
const EditTitle = document.getElementById("Title");
const EditCategory = document.getElementById("Category");
const EditTimeStart = document.getElementById("TimeStart");
const EditTimeEnd = document.getElementById("TimeEnd");

let currentTaskData = null;

const currentPath = window.location.pathname;
const viewsIndex = currentPath.lastIndexOf("/views/");
const baseUrl = viewsIndex !== -1 ? currentPath.substring(0, viewsIndex) : "";

//one of the Edit buttons was clicked
Buttons.forEach(function(Button) {
    Button.addEventListener("click", function() {
        EditBlock.style.display = "flex";
        
        //check the selected task
        let selectedTask = this.value;
        
        //get selected task
        getData(selectedTask);
    });
});

CloseField.addEventListener("click", function() {
    EditBlock.style.display = "none";
});

//update button clicked
UpdateTask.addEventListener("click", function() {
    if (!currentTaskData) {
        console.error("No data found");
        return;
    }
    
    //update task
    UpdateData();
});

async function UpdateData(){
    const formData = new FormData();

    if (currentTaskData.SlotID) {
        formData.append("slot_id", currentTaskData.SlotID);
    }
    formData.append("task_id", currentTaskData.task_id);
    
    if (currentTaskData.Date) {
        formData.append("slot_date", currentTaskData.Date);
    }

    const personeelHidden = document.getElementById("editPersoneelHidden");
    if (personeelHidden) {
        formData.append("personeel", personeelHidden.value);
    }
    
    formData.append("title", EditTitle.value);
    formData.append("start_time", EditTimeStart.value);
    formData.append("end_time", EditTimeEnd.value);

    //send to update_task.php
    const response = await fetch(`${baseUrl}/api/tasks/update_task.php`, {
        method: "POST",
        body: formData,
    });

    const result = await response.json();
    console.log(result);
    
    if (result.success) {
        alert("Taak succesvol bijgewerkt!");
        EditBlock.style.display = "none";
        location.reload();
    } else {
        alert("Error: " + result.message);
    }
}


async function getData(selectedTask){

    const response = await fetch(`${baseUrl}/api/tasks/get_task.php?task_id=${selectedTask}`, {
      method: "GET",
    });

    const result = await response.json();

    currentTaskData = result;
    currentTaskData.task_id = selectedTask;

    Frequency = result['frequency']
    if(Frequency == null){
        Frequency = "ONCE"
        
    }

    console.log(result);

    EditTitle.value = result['title'];
    EditTimeStart.value = result['timeStart'];
    EditTimeEnd.value = result['timeEnd'];

    
    await loadCategoriesAndSelect(result['category']);
}

async function loadCategoriesAndSelect(categoryId) {
    const response = await fetch(baseUrl + '/api/categories/get_categories.php');
    const data = await response.json();
    
    if (!EditCategory) return;
    EditCategory.innerHTML = '<option value="">Selecteer categorie...</option>';
    
    if (data.success && data.categories.length) {
        data.categories.forEach(cat => {
            EditCategory.innerHTML += `<option value="${cat.category_id}">${cat.name}</option>`;
        });
    }

    EditCategory.value = categoryId;
}

//delete button clicked
DeleteTaskButton.addEventListener("click", function() {
    if (!currentTaskData) {
        console.error("No data found");
        return;
    }

    //delete task
    DeleteTask();
});

async function DeleteTask(){
    const formData = new FormData();
    
    if (currentTaskData.SlotID) {
        formData.append("slot_id", currentTaskData.SlotID);
    }
    if (currentTaskData.task_id) {
        formData.append("task_id", currentTaskData.task_id);
    }

    const response = await fetch(`${baseUrl}/api/tasks/delete_task.php`, {
        method: "POST",
        body: formData,
    });

    const result = await response.json();
    console.log(result);
    
    if (result.success) {
        alert("Taak succesvol verwijderd!");
        EditBlock.style.display = "none";
        location.reload();
    } else {
        alert("Error: " + result.message);
    }
}