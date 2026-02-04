const Buttons = document.querySelectorAll(".Edit");
const EditBlock = document.getElementById("EditBlock");
const EditTitle = document.getElementById("Title");
const EditDescription = document.getElementById("Description");
const EditFrequency = document.getElementById("Frequency")
const EditDate = document.getElementById("Date");

const currentPath = window.location.pathname;
const viewsIndex = currentPath.lastIndexOf("/views/");
const baseUrl = viewsIndex !== -1 ? currentPath.substring(0, viewsIndex) : "";

Buttons.forEach(function(Button) {
    Button.addEventListener("click", function() {
        EditBlock.style.display = "flex";
        
        let selectedTask = this.value;
        
        getData(selectedTask);
    });
});

async function getData(selectedTask){

    const response = await fetch(`${baseUrl}/api/tasks/get_task.php?task_id=${selectedTask}`, {
      method: "GET",
    });

    const result = await response.json();

    Frequency = result['frequency']
    if(Frequency == null){
        Frequency = "ONCE"
    }

    console.log(result['Date']);
    console.log(Frequency);
    console.log(result['title'])

    //display all the information of the selected task.
    EditTitle.value = result['title'];
    EditDescription.value = result['description'];
    EditFrequency.value = Frequency;
    EditDate.value = result['Date'];
}
