// Automatische path detectie - werkt op elke PC
const currentPath = window.location.pathname;
const viewsIndex = currentPath.lastIndexOf("/views/");
const baseUrl = viewsIndex !== -1 ? currentPath.substring(0, viewsIndex) : "";

// Kleur mapping voor week-events
function getEventClass(colorHex) {
  switch (colorHex) {
    case "#cccccc":
      return "green-bg";
    default:
      return "green-bg";
  }
}

// Render week events in een kolom
function renderWeekEvents(dayColumn, events) {
  const isAdmin = typeof userIsAdmin !== "undefined" && userIsAdmin === true;
  
  events.forEach((ev) => {
    const el = document.createElement("div");
    
    // Bepaal de kleur op basis van inschrijfstatus (voor niet-admins)
    let colorClass = getEventClass(ev.color); // Standaard kleur
    
    if (!isAdmin) {
      const capacity = ev.capacity || 1;
      const registered = ev.registered_count || 0;
      const isFull = registered >= capacity;
      const userRegistered = ev.user_is_registered || false;
      
      // Groen: ingeschreven
      // Oranje: niet ingeschreven, nog plek
      // Rood: vol
      if (userRegistered) {
        colorClass = 'green-bg';
      } else if (isFull) {
        colorClass = 'red-bg';
      } else {
        colorClass = 'orange-bg';
      }
    }
    
    el.classList.add("week-event", colorClass);
    el.innerHTML = `
            <div class="event-time">${ev.start} - ${ev.end}</div>
            <div class="event-title">${ev.title}</div>
        `;
    dayColumn.appendChild(el);
  });
}

// Vul weekview met events
function populateWeekView(tasksByDate) {
  const dayColumns = document.querySelectorAll(".week-day-column");
  dayColumns.forEach((col) => {
    const date = col.dataset.date;
    col.innerHTML = "";
    if (tasksByDate[date]) {
      renderWeekEvents(col, tasksByDate[date]);
      col.classList.add("active-column");
    }
  });
}

// Zet de data-date attribuut op elke kolom
function setWeekDates(startDate) {
  const cols = document.querySelectorAll(".week-day-column");
  cols.forEach((col, i) => {
    const d = new Date(startDate);
    d.setDate(d.getDate() + i);
    const iso = d.toISOString().split("T")[0];
    col.dataset.date = iso;
  });
}

// Enable clicking on week day columns to show tasks modal
function enableWeekDayClick(tasksByDate) {
  document.querySelectorAll(".week-day-column").forEach((col) => {
    col.onclick = () => {
      const date = col.dataset.date;
      const events = tasksByDate[date] || [];
      showTasksModal(date, events);
    };
  });
}
// Modal voor taken tonen
function showTasksModal(date, events) {
  if (!events || events.length === 0) {
    alert("Geen taken op deze dag");
    return;
  }
  let modal = document.getElementById("tasksModal");
  if (!modal) {
    modal = document.createElement("div");
    modal.id = "tasksModal";
    modal.style.position = "fixed";
    modal.style.top = "0";
    modal.style.left = "0";
    modal.style.width = "100vw";
    modal.style.height = "100vh";
    modal.style.background = "rgba(0,0,0,0.3)";
    modal.style.display = "flex";
    modal.style.alignItems = "center";
    modal.style.justifyContent = "center";
    modal.style.zIndex = "9999";
    document.body.appendChild(modal);
  }

  const isAdmin = typeof userIsAdmin !== "undefined" && userIsAdmin === true;

  modal.innerHTML = `<div class="task-modal-content" style="background:#fff;border-radius:16px;max-width:500px;width:90vw;padding:0;box-shadow:0 2px 16px rgba(0,0,0,0.15);overflow:hidden;">
        <div style='display:flex;align-items:center;gap:12px;padding:16px 20px 0 20px;'>
            <div style='background:#e5dbfa;color:#6b5b95;width:32px;height:32px;display:flex;align-items:center;justify-content:center;border-radius:50%;font-weight:600;'>A</div>
            <span style='font-weight:600;font-size:18px;'>Taak Details</span>
            <button id='closeTasksModal' style='margin-left:auto;background:none;border:none;font-size:22px;cursor:pointer;'>&times;</button>
        </div>
        <div style='background:#f4f0fa;display:flex;align-items:center;justify-content:center;height:100px;margin:20px 0;'>
            <svg width='60' height='60' fill='#d1c4e9' viewBox='0 0 24 24'><circle cx='12' cy='12' r='10'/></svg>
        </div>
        <div style='padding:0 20px 20px 20px;max-height:400px;overflow-y:auto;'>
            ${events
              .map(
                (ev, idx) => {
                const capacity = ev.capacity || 1;
                const registered = ev.registered_count || 0;
                const spotsLeft = capacity - registered;
                const isFull = spotsLeft <= 0;
                const userRegistered = ev.user_is_registered || false;

                return `
                <div class='task-item-modal' style='margin-bottom:18px;padding:16px;background:#f9f9f9;border-radius:8px;position:relative;' data-slot-id='${ev.slot_id || ""}' data-task-id='${ev.task_id || ""}'>
                    <div style='font-weight:600;font-size:17px;margin-bottom:6px;'>${ev.title}</div>
                    <div style='color:#888;font-size:14px;margin-bottom:8px;'>
                        <span style='display:inline-block;margin-right:12px;'>🕐 ${ev.start} - ${ev.end}</span>
                        ${ev.frequency ? `<span style='background:#e5dbfa;color:#6b5b95;padding:2px 8px;border-radius:4px;font-size:12px;'>${ev.frequency}</span>` : ""}
                    </div>
                    
                    ${
                      !isAdmin
                        ? `
                    <div style='background:#fff;padding:10px;border-radius:6px;margin:10px 0;border-left:3px solid ${isFull ? "#dc3545" : "#28a745"};'>
                        <div style='font-size:13px;color:#666;margin-bottom:4px;'>Beschikbare plekken</div>
                        <div style='font-size:18px;font-weight:600;color:${isFull ? "#dc3545" : "#28a745"};'>
                            ${registered} / ${capacity} ${isFull ? "(VOL)" : ""}
                        </div>
                    </div>
                    `
                        : ""
                    }
                    
                    <div class='task-members-${ev.slot_id}' style='margin:8px 0;font-size:14px;color:#666;'>
                        <div style='font-weight:500;margin-bottom:4px;'>Ingeschreven leden:</div>
                        <div style='font-style:italic;color:#999;'>Laden...</div>
                    </div>
                    ${
                      isAdmin && (ev.slot_id || ev.task_id)
                        ? `
                    <div style='margin-top:12px;display:flex;gap:8px;'>
                        <button class='edit-task-btn' data-slot-id='${ev.slot_id || ""}' data-task-id='${ev.task_id || ""}' data-frequency='${ev.frequency || ""}' data-slot-date='${ev.slot_date || ""}' style='flex:1;background:#6b5b95;color:#fff;border:none;padding:8px 16px;border-radius:6px;cursor:pointer;font-size:14px;'>
                            ✏️ Bewerken
                        </button>
                        <button class='delete-task-btn' data-slot-id='${ev.slot_id || ""}' data-task-id='${ev.task_id || ""}' data-frequency='${ev.frequency || ""}' style='flex:1;background:#dc3545;color:#fff;border:none;padding:8px 16px;border-radius:6px;cursor:pointer;font-size:14px;'>
                            🗑️ Verwijderen
                        </button>
                    </div>
                    `
                        : !isAdmin && ev.slot_id
                        ? `
                    <div class='signup-container-${ev.slot_id}' style='margin-top:12px;'>
                        ${
                          userRegistered
                            ? `<button class='signout-task-btn' data-slot-id='${ev.slot_id}' style='width:100%;background:#dc3545;color:#fff;border:none;padding:12px 16px;border-radius:8px;cursor:pointer;font-size:15px;font-weight:600;transition:all 0.2s;box-shadow:0 2px 4px rgba(220,53,69,0.3);'>
                              ✗ Uitschrijven
                             </button>`
                            : isFull
                              ? `<button disabled style='width:100%;background:#6c757d;color:#fff;border:none;padding:12px 16px;border-radius:8px;cursor:not-allowed;font-size:15px;font-weight:600;opacity:0.6;'>
                                🚫 Taak is vol
                               </button>`
                              : `<button class='signup-task-btn' data-slot-id='${ev.slot_id}' style='width:100%;background:#28a745;color:#fff;border:none;padding:12px 16px;border-radius:8px;cursor:pointer;font-size:15px;font-weight:600;transition:all 0.2s;box-shadow:0 2px 4px rgba(40,167,69,0.3);'>
                                ✓ Inschrijven voor taak
                               </button>`
                        }
                    </div>
                    `
                        : ""
                    }
                </div>
            `;
              })
              .join("")}
        </div>
    </div>`;
  modal.onclick = function (e) {
    if (e.target === modal) modal.style.display = "none";
  };
  document.getElementById("closeTasksModal").onclick = function () {
    modal.style.display = "none";
  };

  // Laad ingeschreven leden voor elke taak
  events.forEach((ev) => {
    if (ev.slot_id) {
      loadTaskMembers(ev.slot_id);
    }
  });

  // Event listeners voor edit en delete knoppen (admin)
  if (isAdmin) {
    modal.querySelectorAll(".edit-task-btn").forEach((btn) => {
      btn.onclick = function () {
        const slotId = this.dataset.slotId || null;
        const taskId = this.dataset.taskId || null;
        const frequency = this.dataset.frequency || null;
        const slotDate = this.dataset.slotDate || null;
        const task = events.find(
          (e) => e.slot_id == slotId || e.task_id == taskId,
        );
        editTask(slotId, taskId, frequency, task, slotDate);
      };
    });

    modal.querySelectorAll(".delete-task-btn").forEach((btn) => {
      btn.onclick = function () {
        const slotId = this.dataset.slotId || null;
        const taskId = this.dataset.taskId || null;
        const frequency = this.dataset.frequency || null;
        deleteTask(slotId, taskId, frequency);
      };
    });
  } else {
    // Event listeners voor signup/signout knoppen (users)
    modal.querySelectorAll(".signup-task-btn").forEach((btn) => {
      btn.onclick = function () {
        const slotId = this.dataset.slotId;
        handleSignupToggle(slotId, false);
      };
      // Hover effect
      btn.onmouseenter = function () {
        this.style.transform = "translateY(-2px)";
        this.style.boxShadow = "0 4px 8px rgba(40,167,69,0.4)";
      };
      btn.onmouseleave = function () {
        this.style.transform = "translateY(0)";
        this.style.boxShadow = "0 2px 4px rgba(40,167,69,0.3)";
      };
    });

    modal.querySelectorAll(".signout-task-btn").forEach((btn) => {
      btn.onclick = function () {
        const slotId = this.dataset.slotId;
        handleSignupToggle(slotId, true);
      };
      // Hover effect
      btn.onmouseenter = function () {
        this.style.transform = "translateY(-2px)";
        this.style.boxShadow = "0 4px 8px rgba(220,53,69,0.4)";
      };
      btn.onmouseleave = function () {
        this.style.transform = "translateY(0)";
        this.style.boxShadow = "0 2px 4px rgba(220,53,69,0.3)";
      };
    });
  }

  modal.style.display = "flex";
}

// Delete task functie (alleen voor admins)
function deleteTask(slotId, taskId, frequency) {
  let confirmMsg = "Weet je zeker dat je deze taak wilt verwijderen?";

  // Voor frequency taken: waarschuwing dat alle herhalingen verwijderd worden
  if (
    frequency &&
    (frequency === "DAILY" || frequency === "WEEKLY" || frequency === "MONTHLY")
  ) {
    confirmMsg = `Deze taak herhaalt zich ${frequency === "DAILY" ? "dagelijks" : frequency === "WEEKLY" ? "wekelijks" : "maandelijks"}. Weet je zeker dat je ALLE herhalingen wilt verwijderen?`;
  }

  if (!confirm(confirmMsg)) {
    return;
  }

  const formData = new FormData();
  if (slotId) {
    formData.append("slot_id", slotId);
  }
  if (taskId) {
    formData.append("task_id", taskId);
  }

  fetch(`${baseUrl}/api/tasks/delete_task.php`, {
    method: "POST",
    body: formData,
  })
    .then((res) => res.json())
    .then((data) => {
      if (data.success) {
        alert(data.message);
        // Sluit modal
        const modal = document.getElementById("tasksModal");
        if (modal) modal.style.display = "none";
        // Refresh calendar
        regenerateCalendar();
        regenerateWeekView();
      } else {
        alert("Fout: " + data.message);
      }
    })
    .catch((err) => {
      console.error("Delete error:", err);
      alert("Er ging iets fout bij het verwijderen");
    });
}

// Edit task functie (alleen voor admins)
function editTask(slotId, taskId, frequency, task, slotDate) {
  // Sluit de huidige modal
  const modal = document.getElementById("tasksModal");
  if (modal) modal.style.display = "none";

  // Maak een edit modal
  let editModal = document.getElementById("editTaskModal");
  if (!editModal) {
    editModal = document.createElement("div");
    editModal.id = "editTaskModal";
    editModal.style.position = "fixed";
    editModal.style.top = "0";
    editModal.style.left = "0";
    editModal.style.width = "100vw";
    editModal.style.height = "100vh";
    editModal.style.background = "rgba(0,0,0,0.3)";
    editModal.style.display = "flex";
    editModal.style.alignItems = "center";
    editModal.style.justifyContent = "center";
    editModal.style.zIndex = "10000";
    document.body.appendChild(editModal);
  }

  const isFrequencyTask =
    frequency &&
    (frequency === "DAILY" ||
      frequency === "WEEKLY" ||
      frequency === "MONTHLY");
  const frequencyLabel =
    frequency === "DAILY"
      ? "dagelijks"
      : frequency === "WEEKLY"
        ? "wekelijks"
        : frequency === "MONTHLY"
          ? "maandelijks"
          : "";

  editModal.innerHTML = `
    <div style='background:#fff;border-radius:16px;max-width:500px;width:90vw;padding:24px;box-shadow:0 2px 16px rgba(0,0,0,0.15);max-height:90vh;overflow-y:auto;'>
      <h2 style='margin:0 0 20px 0;font-size:20px;'>Taak Bewerken</h2>
      ${isFrequencyTask ? `<div style='background:#fff3cd;border:1px solid #ffc107;padding:12px;border-radius:6px;margin-bottom:16px;font-size:14px;color:#856404;'>⚠️ Deze taak herhaalt zich ${frequencyLabel}. Wijzigingen worden op ALLE herhalingen toegepast.</div>` : ""}
      <form id='editTaskForm'>
        <div style='margin-bottom:16px;'>
          <label style='display:block;margin-bottom:6px;font-weight:600;'>Taak naam</label>
          <input type='text' id='editTitle' value='${task.title}' style='width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;font-size:14px;box-sizing:border-box;' required>
        </div>
        <div style='margin-bottom:16px;display:flex;gap:12px;'>
          <div style='flex:1;'>
            <label style='display:block;margin-bottom:6px;font-weight:600;'>Start tijd</label>
            <input type='time' id='editStartTime' value='${task.start}' style='width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;font-size:14px;box-sizing:border-box;' required>
          </div>
          <div style='flex:1;'>
            <label style='display:block;margin-bottom:6px;font-weight:600;'>Eind tijd</label>
            <input type='time' id='editEndTime' value='${task.end}' style='width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;font-size:14px;box-sizing:border-box;' required>
          </div>
        </div>
        ${
          slotId || taskId
            ? `
        <div style='margin-bottom:16px;'>
          <label style='display:block;margin-bottom:6px;font-weight:600;'>Personeel toevoegen</label>
          <div style='position:relative;'>
            <input type='text' id='editPersoneelInput' placeholder='Zoek personeel...' autocomplete='off' style='width:100%;padding:10px;border:1px solid #ddd;border-radius:6px;font-size:14px;box-sizing:border-box;'>
            <div id='editPersoneelSuggestions' style='display:none;position:absolute;z-index:10;background:#fff;border:1px solid #ddd;border-radius:6px;width:100%;max-height:150px;overflow-y:auto;margin-top:4px;'></div>
          </div>
          <div id='editSelectedPersoneel' style='margin-top:8px;display:flex;flex-wrap:wrap;gap:6px;'></div>
          <input type='hidden' id='editPersoneelHidden'>
          <small style='color:#888;font-size:13px;'>Typ om personeel te zoeken en klik om toe te voegen</small>
        </div>
        `
            : ""
        }
        <div style='display:flex;gap:12px;margin-top:20px;'>
          <button type='button' id='cancelEdit' style='flex:1;padding:10px;background:#ccc;border:none;border-radius:6px;cursor:pointer;font-size:14px;'>Annuleren</button>
          <button type='submit' style='flex:1;padding:10px;background:#6b5b95;color:#fff;border:none;border-radius:6px;cursor:pointer;font-size:14px;'>Opslaan</button>
        </div>
      </form>
    </div>
  `;

  editModal.style.display = "flex";

  // Initialiseer personeel selector (als slotId of taskId bestaat)
  let editSelectedUsers = [];

  if (slotId || taskId) {
    const editPersoneelInput = document.getElementById("editPersoneelInput");
    const editPersoneelSuggestions = document.getElementById(
      "editPersoneelSuggestions",
    );
    const editSelectedPersoneelDiv = document.getElementById(
      "editSelectedPersoneel",
    );
    const editPersoneelHidden = document.getElementById("editPersoneelHidden");

    // Laad huidige toegewezen personeel
    const fetchUrl = slotId 
      ? `${baseUrl}/api/users/get_assigned_users.php?slot_id=${slotId}`
      : `${baseUrl}/api/users/get_assigned_users.php?task_id=${taskId}&slot_date=${encodeURIComponent(slotDate)}`;
    fetch(fetchUrl)
      .then((res) => res.json())
      .then((data) => {
        if (data.success && data.assigned) {
          data.assigned.forEach((user) => {
            addEditSelectedUser(user);
          });
        }
      })
      .catch((err) => console.error("Error loading assigned users:", err));

    // Zoekfunctionaliteit
    editPersoneelInput.addEventListener("input", function () {
      const search = this.value.trim();

      if (search.length < 2) {
        editPersoneelSuggestions.style.display = "none";
        return;
      }

      fetch(
        `${baseUrl}/api/users/get_users.php?search=${encodeURIComponent(search)}`,
      )
        .then((res) => res.json())
        .then((data) => {
          if (data.success && data.users.length > 0) {
            editPersoneelSuggestions.innerHTML = "";
            data.users.forEach((user) => {
              if (editSelectedUsers.find((u) => u.user_id === user.user_id))
                return;

              const roleLabel = user.role_id == 2 ? "Admin" : "User";
              const div = document.createElement("div");
              div.style.padding = "8px 12px";
              div.style.cursor = "pointer";
              div.style.borderBottom = "1px solid #eee";
              div.innerHTML = `<strong>${user.first_name} ${user.last_name}</strong> <span style="color:#888;font-size:13px;">(${roleLabel})</span>`;

              div.addEventListener("click", function () {
                addEditSelectedUser(user);
                editPersoneelInput.value = "";
                editPersoneelSuggestions.style.display = "none";
              });

              div.addEventListener("mouseenter", function () {
                this.style.background = "#f0f0f0";
              });
              div.addEventListener("mouseleave", function () {
                this.style.background = "#fff";
              });

              editPersoneelSuggestions.appendChild(div);
            });
            editPersoneelSuggestions.style.display = "block";
          } else {
            editPersoneelSuggestions.style.display = "none";
          }
        });
    });

    function addEditSelectedUser(user) {
      editSelectedUsers.push(user);

      const badge = document.createElement("span");
      badge.style.background = "#e5dbfa";
      badge.style.color = "#6b5b95";
      badge.style.padding = "4px 8px";
      badge.style.borderRadius = "4px";
      badge.style.fontSize = "14px";
      badge.style.cursor = "pointer";
      badge.dataset.userId = user.user_id;
      badge.textContent = `${user.first_name} ${user.last_name}`;

      badge.addEventListener("click", function () {
        editSelectedUsers = editSelectedUsers.filter(
          (u) => u.user_id !== user.user_id,
        );
        badge.remove();
        updateEditPersoneelHidden();
      });

      editSelectedPersoneelDiv.appendChild(badge);
      updateEditPersoneelHidden();
    }

    function updateEditPersoneelHidden() {
      editPersoneelHidden.value = editSelectedUsers
        .map((u) => u.user_id)
        .join(",");
    }
  }

  document.getElementById("cancelEdit").onclick = function () {
    editModal.style.display = "none";
  };

  editModal.onclick = function (e) {
    if (e.target === editModal) editModal.style.display = "none";
  };

  document.getElementById("editTaskForm").onsubmit = function (e) {
    e.preventDefault();

    // Validatie voor datum en tijd in het verleden
    const editStartTime = document.getElementById("editStartTime").value;
    const editEndTime = document.getElementById("editEndTime").value;
    
    // Check of de slot datum in het verleden ligt
    if (slotDate) {
      const taskDate = new Date(slotDate);
      const today = new Date();
      today.setHours(0, 0, 0, 0);
      taskDate.setHours(0, 0, 0, 0);
      
      // Als de datum in het verleden is
      if (taskDate < today) {
        alert('⚠️ Deze taak is in het verleden. Je kunt geen taken in het verleden bewerken.');
        return false;
      }
      
      // Als de datum vandaag is, controleer de tijd
      if (taskDate.getTime() === today.getTime() && editStartTime) {
        const now = new Date();
        const currentTime = now.getHours() * 60 + now.getMinutes();
        const [startHour, startMin] = editStartTime.split(':').map(Number);
        const selectedTime = startHour * 60 + startMin;
        
        if (selectedTime < currentTime) {
          alert('⚠️ De starttijd ligt in het verleden. Kies een latere tijd.');
          document.getElementById("editStartTime").focus();
          return false;
        }
      }
    }
    
    // Check of eindtijd na starttijd is
    if (editStartTime && editEndTime) {
      const [startHour, startMin] = editStartTime.split(':').map(Number);
      const [endHour, endMin] = editEndTime.split(':').map(Number);
      const startMinutes = startHour * 60 + startMin;
      const endMinutes = endHour * 60 + endMin;
      
      if (endMinutes <= startMinutes) {
        alert('⚠️ Eindtijd moet later zijn dan starttijd.');
        document.getElementById("editEndTime").focus();
        return false;
      }
    }

    const formData = new FormData();
    if (slotId) {
      formData.append("slot_id", slotId);
    }
    if (taskId) {
      formData.append("task_id", taskId);
    }
    if (slotDate) {
      formData.append("slot_date", slotDate);
    }
    // Voeg personeel toe als het veld bestaat
    const personeelHidden = document.getElementById("editPersoneelHidden");
    if (personeelHidden) {
      formData.append("personeel", personeelHidden.value);
    }
    formData.append("title", document.getElementById("editTitle").value);
    formData.append(
      "start_time",
      editStartTime,
    );
    formData.append("end_time", editEndTime);

    fetch(`${baseUrl}/api/tasks/update_task.php`, {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          alert(data.message);
          editModal.style.display = "none";
          // Refresh calendar
          regenerateCalendar();
          regenerateWeekView();
        } else {
          alert("Fout: " + data.message);
        }
      })
      .catch((err) => {
        console.error("Update error:", err);
        alert("Er ging iets fout bij het updaten");
      });
  };
}

// Kleur naar CSS class
function getEventClass(colorHex) {
  switch (colorHex) {
    case "#cccccc":
      return "green-event";
    default:
      return "green-event";
  }
}

// Render events in een dagcel
function renderEventsForDay(dayCell, events) {
  // Toon alleen een blokje, klikbaar voor alle taken
  if (events.length > 0) {
    const el = document.createElement("div");
    
    // Bepaal de kleur op basis van inschrijfstatus (voor niet-admins)
    const isAdmin = typeof userIsAdmin !== "undefined" && userIsAdmin === true;
    let colorClass = getEventClass(events[0].color); // Standaard kleur
    
    if (!isAdmin && events.length === 1) {
      const event = events[0];
      const capacity = event.capacity || 1;
      const registered = event.registered_count || 0;
      const isFull = registered >= capacity;
      const userRegistered = event.user_is_registered || false;
      
      // Groen: ingeschreven
      // Oranje: niet ingeschreven, nog plek
      // Rood: vol
      if (userRegistered) {
        colorClass = 'green';
      } else if (isFull) {
        colorClass = 'red';
      } else {
        colorClass = 'orange';
      }
    }
    
    el.classList.add("event", colorClass);
    el.style.cursor = "pointer";
    let freqLabel = "";
    if (events.length === 1 && events[0].frequency) {
      if (events[0].frequency === "DAILY") freqLabel = "Dagelijks";
      else if (events[0].frequency === "WEEKLY") freqLabel = "Wekelijks";
      else if (events[0].frequency === "MONTHLY") freqLabel = "Maandelijks";
    }
    el.innerHTML = `
            <h3 style='margin:0;font-size:15px;'>${events.length === 1 ? events[0].title : events.length + " taken"}</h3>
            <p style='margin:0;font-size:13px;color:#888;'>${freqLabel ? freqLabel : "Klik voor details"}</p>
        `;
    el.onclick = (e) => {
      e.stopPropagation();
      showTasksModal("", events);
    };
    dayCell.appendChild(el);
  }
}
// Toggle between week and month view
const toggleBtns = document.querySelectorAll(".toggle-btn");
const monthView = document.querySelector(".month-view");
const weekView = document.querySelector(".week-view");
const mobileTasksSection = document.querySelector(".mobile-tasks-section");
const monthNavigation = document.querySelector(".month-navigation");
const weekNavigationBtns = document.querySelectorAll(".week-navigation");

toggleBtns.forEach((btn) => {
  btn.addEventListener("click", function () {
    toggleBtns.forEach((b) => b.classList.remove("active"));
    this.classList.add("active");

    const view = this.dataset.view;

    if (view === "week") {
      monthView.style.display = "none";
      weekView.style.display = "block";
      weekView.classList.add("active");
      // Show week navigation
      if (monthNavigation) {
        monthNavigation.style.display = "none";
      }
      weekNavigationBtns.forEach((nav) => {
        nav.style.display = "flex";
      });
      // Toon weeknavigatie altijd in weekview
      const weekNav = document.getElementById("weekNavigation");
      if (weekNav) weekNav.style.display = "flex";
      // Hide mobile tasks section in week view
      if (mobileTasksSection && window.innerWidth <= 768) {
        mobileTasksSection.style.display = "none";
      }
      // Initialize week view
      regenerateWeekView();
      updateWeekInfo();
    } else {
      monthView.style.display = "block";
      weekView.style.display = "none";
      weekView.classList.remove("active");
      // Show month navigation buttons
      if (monthNavigation) {
        monthNavigation.style.display = "flex";
      }
      weekNavigationBtns.forEach((nav) => {
        nav.style.display = "none";
      });
      // Verberg weeknavigatie in maandview
      const weekNav = document.getElementById("weekNavigation");
      if (weekNav) weekNav.style.display = "none";
      // Show mobile tasks section in month view
      if (mobileTasksSection && window.innerWidth <= 768) {
        mobileTasksSection.style.display = "block";
      }
    }

    console.log("Switched to " + view + " view");
  });
});

// Account button click
const accountBtn = document.querySelector(".account-btn");
const createTaskBtn = document.querySelector(".create-task-btn");
const taakModal = document.getElementById("taakModal");
const taakForm = document.getElementById("taakForm");
const fileUpload = document.querySelector(".file-upload");
const fotoInput = document.getElementById("fotoInput");

// Open modal functie
function openTaakModal() {
  taakModal.classList.add("active");
  document.body.style.overflow = "hidden";
}

// Close modal functie
function closeTaakModal() {
  taakModal.classList.remove("active");
  document.body.style.overflow = "";
}

// Open modal bij klik op "Taak aanmaken" knop
if (accountBtn) {
  accountBtn.addEventListener("click", function (e) {
    e.preventDefault();
    openTaakModal();
  });
}

// Open modal bij klik op mobiele "Taak aanmaken" knop
if (createTaskBtn) {
  createTaskBtn.addEventListener("click", function (e) {
    e.preventDefault();
    openTaakModal();
  });
}

// Sluit modal bij klik buiten de modal content (alleen voor admins)
if (taakModal) {
  taakModal.addEventListener("click", function (e) {
    if (e.target === taakModal) {
      closeTaakModal();
    }
  });
}

// File upload click handler
if (fileUpload && fotoInput) {
  fileUpload.addEventListener("click", function () {
    fotoInput.click();
  });

  fotoInput.addEventListener("change", function (e) {
    if (this.files && this.files[0]) {
      const fileName = this.files[0].name;
      const uploadIcon = fileUpload.querySelector(".upload-icon p");
      if (uploadIcon) {
        uploadIcon.textContent = fileName;
      }
    }
  });
}

// Form submit handler
if (taakForm) {
  taakForm.addEventListener("submit", async function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    const submitBtn = this.querySelector(".submit-btn");
    submitBtn.disabled = true;
    submitBtn.textContent = "Bezig...";
    try {
      const response = await fetch(baseUrl + "/api/tasks/create_task.php", {
        method: "POST",
        body: formData,
      });
      const data = await response.json();
      if (data.success) {
        alert(data.message);
        this.reset();
        closeTaakModal();
      } else {
        alert(data.message);
      }
    } catch (err) {
      alert("Er is een fout opgetreden. Probeer het opnieuw.");
    } finally {
      submitBtn.disabled = false;
      submitBtn.textContent = "Taak aanmaken";
    }
  });
}

// Event click handler
const events = document.querySelectorAll(".event");
events.forEach((event) => {
  event.addEventListener("click", function (e) {
    e.stopPropagation();
    const title = this.querySelector("h3").textContent;
    const author = this.querySelector("p").textContent;
    console.log("Event clicked:", title, "by", author);
    // Hier kun je een modal openen met event details
  });
});

// Day cell click handler
const dayCells = document.querySelectorAll(".day-cell");
dayCells.forEach((cell) => {
  cell.addEventListener("click", function () {
    if (!this.querySelector(".event")) {
      console.log("Empty day clicked - add new event");
      // Hier kun je een "add event" dialog tonen
    }
  });
});

// Profile circle click
const profileCircle = document.querySelector(".profile-circle");
const logoutModal = document.getElementById("logoutModal");
const cancelLogoutBtn = document.querySelector(".cancel-logout-btn");
const confirmLogoutBtn = document.querySelector(".confirm-logout-btn");

if (profileCircle) {
  profileCircle.addEventListener("click", function (e) {
    e.preventDefault();
    // Open logout modal
    logoutModal.classList.add("active");
    document.body.style.overflow = "hidden";
  });
}

// Close logout modal functions
function closeLogoutModal() {
  logoutModal.classList.remove("active");
  document.body.style.overflow = "";
}

if (cancelLogoutBtn) {
  cancelLogoutBtn.addEventListener("click", closeLogoutModal);
}

// Close modal when clicking outside
if (logoutModal) {
  logoutModal.addEventListener("click", function (e) {
    if (e.target === logoutModal) {
      closeLogoutModal();
    }
  });
}

// Confirm logout
if (confirmLogoutBtn) {
  confirmLogoutBtn.addEventListener("click", function () {
    // Redirect to logout page
    window.location.href = "logout.php";
  });
}

// Month navigation functionality
let currentMonth = new Date().getMonth(); // 0-11
let currentYear = new Date().getFullYear();

const monthNames = [
  "Januari",
  "Februari",
  "Maart",
  "April",
  "Mei",
  "Juni",
  "Juli",
  "Augustus",
  "September",
  "Oktober",
  "November",
  "December",
];

const prevMonthBtn = document.getElementById("prevMonth");
const nextMonthBtn = document.getElementById("nextMonth");
const calendarTitle = document.querySelector(".calendar-title");
const weekInfoLabel = document.querySelector(
  ".current-week-info .info-label:last-of-type",
);

// Update calendar title with current month
function updateCalendarTitle() {
  const monthYear = `${monthNames[currentMonth]} ${currentYear}`;

  if (calendarTitle) {
    calendarTitle.textContent = monthYear;
  }

  if (weekInfoLabel) {
    weekInfoLabel.textContent = monthYear;
  }
}

// Navigate to previous month
if (prevMonthBtn) {
  prevMonthBtn.addEventListener("click", function () {
    currentMonth--;
    if (currentMonth < 0) {
      currentMonth = 11;
      currentYear--;
    }
    updateCalendarTitle();
    regenerateCalendar();
  });
}

// Navigate to next month
if (nextMonthBtn) {
  nextMonthBtn.addEventListener("click", function () {
    currentMonth++;
    if (currentMonth > 11) {
      currentMonth = 0;
      currentYear++;
    }
    updateCalendarTitle();
    regenerateCalendar();
  });
}

// Regenerate calendar grid for new month
function regenerateCalendar() {
  const daysGrid = document.querySelector(".days-grid");
  const weekNumbers = document.querySelector(".week-numbers");
  if (!daysGrid) return;

  // Get first day of month and number of days
  const firstDay = new Date(currentYear, currentMonth, 1).getDay();
  const daysInMonth = new Date(currentYear, currentMonth + 1, 0).getDate();
  const daysInPrevMonth = new Date(currentYear, currentMonth, 0).getDate();

  // Adjust for Monday start (0 = Sunday, make it 6)
  const adjustedFirstDay = firstDay === 0 ? 6 : firstDay - 1;

  // Clear existing days
  daysGrid.innerHTML = "";

  // Clear and rebuild week numbers
  if (weekNumbers) {
    weekNumbers.innerHTML = '<div class="week-header"></div>';

    // Calculate week numbers for this month
    const totalDays = adjustedFirstDay + daysInMonth;
    const weeksToShow = Math.ceil(totalDays / 7);

    for (let week = 0; week < weeksToShow; week++) {
      const weekDate = new Date(
        currentYear,
        currentMonth,
        1 + week * 7 - adjustedFirstDay,
      );
      const weekNum = getWeekNumber(weekDate);
      const weekDiv = document.createElement("div");
      weekDiv.className = "week-number";

      // Check if this is the current week
      const today = new Date();
      const currentWeekNum = getWeekNumber(today);
      if (weekNum === currentWeekNum) {
        weekDiv.classList.add("current-week");
      }

      weekDiv.textContent = weekNum;
      weekNumbers.appendChild(weekDiv);
    }
  }

  // Add previous month's days
  for (let i = adjustedFirstDay - 1; i >= 0; i--) {
    const dayCell = document.createElement("div");
    dayCell.className = "day-cell prev-month";
    dayCell.textContent = daysInPrevMonth - i;
    daysGrid.appendChild(dayCell);
  }

  // Add current month's days first
  for (let day = 1; day <= daysInMonth; day++) {
    const dayCell = document.createElement("div");
    dayCell.className = "day-cell current-month";

    // Check if it's today
    const today = new Date();
    if (
      day === today.getDate() &&
      currentMonth === today.getMonth() &&
      currentYear === today.getFullYear()
    ) {
      dayCell.classList.add("today");
    }

    // Check if it's weekend (Saturday of Sunday)
    const dayOfWeek = new Date(currentYear, currentMonth, day).getDay();
    if (dayOfWeek === 0 || dayOfWeek === 6) {
      dayCell.classList.add("weekend");
    }

    dayCell.textContent = day;
    daysGrid.appendChild(dayCell);
  }

  // Add next month's days to fill the grid
  const totalCells = adjustedFirstDay + daysInMonth;
  const remainingCells = Math.ceil(totalCells / 7) * 7 - totalCells;

  for (let day = 1; day <= remainingCells; day++) {
    const dayCell = document.createElement("div");
    dayCell.className = "day-cell next-month";
    dayCell.textContent = day;
    daysGrid.appendChild(dayCell);
  }

  // Haal taken op en render ze in de juiste dag
  const year = currentYear;
  const month = currentMonth;
  const start = `${year}-${String(month + 1).padStart(2, "0")}-01`;
  const end = `${year}-${String(month + 1).padStart(2, "0")}-${String(daysInMonth).padStart(2, "0")}`;

  // Maak tasksByDate globaal zodat andere functies het kunnen gebruiken
  window.tasksByDate = {};
  fetch(`${baseUrl}/api/tasks/get_calendar_tasks.php?start=${start}&end=${end}`)
    .then((res) => res.json())
    .then((data) => {
      window.tasksByDate = data;
      const tasksByDate = window.tasksByDate;
      
      // Loop door alle day cells en voeg events toe
      const allDayCells = daysGrid.querySelectorAll(".day-cell.current-month");
      allDayCells.forEach((dayCell, index) => {
        const day = index + 1;
        const dateKey = `${year}-${String(month + 1).padStart(2, "0")}-${String(day).padStart(2, "0")}`;
        if (tasksByDate[dateKey]) {
          renderEventsForDay(dayCell, tasksByDate[dateKey]);
        }
      });
    });

  // Re-add click handlers to new day cells
  const newDayCells = daysGrid.querySelectorAll(".day-cell");
  newDayCells.forEach((cell) => {
    cell.addEventListener("click", function () {
      if (!this.querySelector(".event")) {
        console.log("Empty day clicked - add new event");
      }
    });
  });

  // Generate mobile calendar - horizontal layout with weekdays as rows
  const mobileCalendar = document.querySelector(".mobile-calendar");
  if (mobileCalendar) {
    mobileCalendar.innerHTML = "";

    // Create mobile calendar table with horizontal layout
    const table = document.createElement("table");
    table.className = "mobile-calendar-table";

    const tbody = document.createElement("tbody");

    // Days of week labels
    const daysOfWeek = ["SUN", "MON", "TUE", "WED", "THUR", "FRI", "Sat"];

    // Build array of all days with their weekday
    const calendarDays = [];
    for (let day = 1; day <= daysInMonth; day++) {
      const date = new Date(currentYear, currentMonth, day);
      const weekday = date.getDay(); // 0 = Sunday, 6 = Saturday
      calendarDays.push({ day, weekday, date });
    }

    // Group days by weekday (0-6)
    const daysByWeekday = [[], [], [], [], [], [], []];
    // Definieer 'today' zodat deze beschikbaar is
    const today = new Date();
    // Gebruik window.tasksByDate als fallback als tasksByDate niet bestaat
    const safeTasksByDate =
      typeof tasksByDate !== "undefined"
        ? tasksByDate
        : window.tasksByDate || {};
    calendarDays.forEach(({ day, weekday, date }) => {
      const dateKey = `${year}-${String(month + 1).padStart(2, "0")}-${String(day).padStart(2, "0")}`;
      const hasTasks =
        safeTasksByDate[dateKey] && safeTasksByDate[dateKey].length > 0;
      const isToday =
        day === today.getDate() &&
        currentMonth === today.getMonth() &&
        currentYear === today.getFullYear();

      daysByWeekday[weekday].push({ day, date, dateKey, hasTasks, isToday });
    });

    // Create one row per weekday
    for (let weekday = 0; weekday < 7; weekday++) {
      const weekdayRow = document.createElement("tr");

      // First cell: weekday label
      const labelCell = document.createElement("td");
      labelCell.className = "day-label";
      labelCell.textContent = daysOfWeek[weekday];
      weekdayRow.appendChild(labelCell);

      // Add cells for each occurrence of this weekday in the month
      const daysForThisWeekday = daysByWeekday[weekday];
      daysForThisWeekday.forEach(({ day, dateKey, hasTasks, isToday }) => {
        const dayCell = document.createElement("td");
        dayCell.textContent = day;

        // Add green background if there are tasks
        if (hasTasks) {
          dayCell.classList.add("green-cell");
        }

        // Add today class if it's today
        if (isToday) {
          dayCell.classList.add("today");
        }

        // Make clickable to show tasks
        if (hasTasks) {
          dayCell.style.cursor = "pointer";
          dayCell.addEventListener("click", () => {
            showTasksModal(tasksByDate[dateKey], dateKey);
          });
        }

        weekdayRow.appendChild(dayCell);
      });

      tbody.appendChild(weekdayRow);
    }

    table.appendChild(tbody);
    mobileCalendar.appendChild(table);
  }
}

// Helper function to get ISO week number
function getWeekNumber(date) {
  const d = new Date(
    Date.UTC(date.getFullYear(), date.getMonth(), date.getDate()),
  );
  const dayNum = d.getUTCDay() || 7;
  d.setUTCDate(d.getUTCDate() + 4 - dayNum);
  const yearStart = new Date(Date.UTC(d.getUTCFullYear(), 0, 1));
  return Math.ceil(((d - yearStart) / 86400000 + 1) / 7);
}

// Week navigation
let currentWeekDate = new Date();
const prevWeekBtn = document.getElementById("prevWeek");
const nextWeekBtn = document.getElementById("nextWeek");
const weekBadge = document.querySelector(".week-badge");
const selectedWeekNum = document.getElementById("selectedWeekNum");

// Navigate to previous week
if (prevWeekBtn) {
  prevWeekBtn.addEventListener("click", function () {
    currentWeekDate.setDate(currentWeekDate.getDate() - 7);
    updateWeekInfo();
    regenerateWeekView();
  });
}

// Navigate to next week
if (nextWeekBtn) {
  nextWeekBtn.addEventListener("click", function () {
    currentWeekDate.setDate(currentWeekDate.getDate() + 7);
    updateWeekInfo();
    regenerateWeekView();
  });
}

// Update week info display
function updateWeekInfo() {
  const selectedWeek = getWeekNumber(currentWeekDate);

  // Update selected week (the one you're navigating through)
  if (selectedWeekNum) {
    selectedWeekNum.textContent = selectedWeek;
  }

  // Keep the actual current week badge unchanged
  // weekBadge stays as the real current week

  // Update month and year in week info
  const monthYear = `${monthNames[currentWeekDate.getMonth()]} ${currentWeekDate.getFullYear()}`;
  if (weekInfoLabel) {
    weekInfoLabel.textContent = monthYear;
  }
}

// Regenerate week view for current week
function regenerateWeekView() {
  const weekGrid = document.querySelector(".week-grid");
  const weekDaysContainer = document.querySelector(".week-days-container");
  const mobileWeekView = document.querySelector(".mobile-week-view");

  if (!weekGrid) return;

  // Calculate start of week (Sunday)
  const weekStart = new Date(currentWeekDate);
  const day = weekStart.getDay();
  const diff = weekStart.getDate() - day;
  weekStart.setDate(diff);

  const daysOfWeek = ["SUN", "MON", "TUE", "WED", "THUR", "FRI", "SAT"];
  const monthNames = [
    "January",
    "February",
    "March",
    "April",
    "May",
    "June",
    "July",
    "August",
    "September",
    "October",
    "November",
    "December",
  ];

  // Clear and rebuild desktop week headers
  weekGrid.innerHTML = "";
  const today = new Date();
  for (let i = 0; i < 7; i++) {
    const dayDate = new Date(weekStart);
    dayDate.setDate(weekStart.getDate() + i);
    const isToday = dayDate.toDateString() === today.toDateString();
    const dayNumber = dayDate.getDate();
    const displayDate = isToday
      ? `${monthNames[dayDate.getMonth()]} ${dayNumber}`
      : dayNumber;
    const dayHeader = document.createElement("div");
    dayHeader.className = "week-day-header";
    if (isToday) dayHeader.classList.add("active-day");
    dayHeader.innerHTML = `
            <div class="day-label">${daysOfWeek[i]}</div>
            <div class="day-number">${displayDate}</div>
        `;
    weekGrid.appendChild(dayHeader);
  }

  // Clear desktop week days container
  if (weekDaysContainer) {
    weekDaysContainer.innerHTML = "";
    // Maak 7 kolommen aan met data-date
    for (let i = 0; i < 7; i++) {
      const dayDate = new Date(weekStart);
      dayDate.setDate(weekStart.getDate() + i);
      const column = document.createElement("div");
      column.className = "week-day-column";
      weekDaysContainer.appendChild(column);
    }
    setWeekDates(weekStart);
    // Haal taken op voor deze week
    const weekStartStr = `${weekStart.getFullYear()}-${String(weekStart.getMonth() + 1).padStart(2, "0")}-${String(weekStart.getDate()).padStart(2, "0")}`;
    const weekEndDate = new Date(weekStart);
    weekEndDate.setDate(weekStart.getDate() + 6);
    const weekEndStr = `${weekEndDate.getFullYear()}-${String(weekEndDate.getMonth() + 1).padStart(2, "0")}-${String(weekEndDate.getDate()).padStart(2, "0")}`;
    fetch(
      `${baseUrl}/api/tasks/get_calendar_tasks.php?start=${weekStartStr}&end=${weekEndStr}`,
    )
      .then((res) => res.json())
      .then((tasksByDate) => {
        populateWeekView(tasksByDate);
        enableWeekDayClick(tasksByDate); // 👈 nieuw
      });
  }

  // Clear and rebuild mobile week view
  if (mobileWeekView) {
    mobileWeekView.innerHTML = "";
    // Haal taken op voor deze week
    const weekStartStr = `${weekStart.getFullYear()}-${String(weekStart.getMonth() + 1).padStart(2, "0")}-${String(weekStart.getDate()).padStart(2, "0")}`;
    const weekEndDate = new Date(weekStart);
    weekEndDate.setDate(weekStart.getDate() + 6);
    const weekEndStr = `${weekEndDate.getFullYear()}-${String(weekEndDate.getMonth() + 1).padStart(2, "0")}-${String(weekEndDate.getDate()).padStart(2, "0")}`;
    fetch(
      `${baseUrl}/api/tasks/get_calendar_tasks.php?start=${weekStartStr}&end=${weekEndStr}`,
    )
      .then((res) => res.json())
      .then((tasksByDate) => {
        for (let i = 0; i < 7; i++) {
          const dayDate = new Date(weekStart);
          dayDate.setDate(weekStart.getDate() + i);
          const dayNumber = dayDate.getDate();
          const dateKey = `${dayDate.getFullYear()}-${String(dayDate.getMonth() + 1).padStart(2, "0")}-${String(dayDate.getDate()).padStart(2, "0")}`;
          const events = tasksByDate[dateKey] || [];
          const mobileDay = document.createElement("div");
          mobileDay.className = "mobile-week-day";
          let contentClass = "";
          if (events.length > 0 && events[0].color === "#cccccc")
            contentClass = "green-day";
          if (events.length > 0 && events[0].color === "#ffb8d1")
            contentClass = "pink-day";
          // Genereer de dagcel
          mobileDay.innerHTML = `
                        <div class="mobile-day-label">${daysOfWeek[i]}</div>
                        <div class="mobile-day-content ${contentClass}">
                            <div class="mobile-day-number">${dayNumber}</div>
                            ${events
                              .map(
                                (ev, idx) => `
                                <div class=\"mobile-week-event\" data-idx=\"${idx}\" style=\"cursor:pointer;\">
                                    <div class=\"event-time\">${ev.start} - ${ev.end}</div>
                                    <div class=\"event-title\">${ev.title}</div>
                                </div>
                            `,
                              )
                              .join("")}
                            ${events.length > 0 ? `<div class=\"event-author\">${events[0].author || ""}</div>` : ""}
                        </div>
                    `;
          // Click handler op dag zelf (voor dagen zonder taken)
          mobileDay.onclick = function (e) {
            // Alleen uitvoeren als er niet op een taak geklikt is
            if (!e.target.closest(".mobile-week-event")) {
              showMobileTasksForDay(dateKey, events);
            }
          };
          // Click handler op elke taak
          setTimeout(() => {
            const eventDivs = mobileDay.querySelectorAll(".mobile-week-event");
            eventDivs.forEach((evDiv) => {
              evDiv.onclick = function (e) {
                e.stopPropagation();
                showMobileTasksForDay(dateKey, events);
              };
            });
          }, 0);
          mobileWeekView.appendChild(mobileDay);
        }
      });
  }
}

// Personeel zoeken en selecteren
const personeelInput = document.getElementById("personeelInput");
const personeelSuggestions = document.getElementById("personeelSuggestions");
const selectedPersoneelDiv = document.getElementById("selectedPersoneel");
const personeelHidden = document.getElementById("personeelHidden");
let selectedUsers = []; // Array met geselecteerde user_id's

if (personeelInput) {
  // Zoek gebruikers terwijl je typt
  personeelInput.addEventListener("input", function () {
    const search = this.value.trim();

    if (search.length < 2) {
      personeelSuggestions.style.display = "none";
      return;
    }

    fetch(
      `${baseUrl}/api/users/get_users.php?search=${encodeURIComponent(search)}`,
    )
      .then((res) => res.json())
      .then((data) => {
        if (data.success && data.users.length > 0) {
          personeelSuggestions.innerHTML = "";
          data.users.forEach((user) => {
            // Skip als al geselecteerd
            if (selectedUsers.find((u) => u.user_id === user.user_id)) return;

            const roleLabel = user.role_id == 2 ? "Admin" : "User";
            const div = document.createElement("div");
            div.style.padding = "8px 12px";
            div.style.cursor = "pointer";
            div.style.borderBottom = "1px solid #eee";
            div.innerHTML = `<strong>${user.first_name} ${user.last_name}</strong> <span style="color:#888;font-size:13px;">(${roleLabel})</span>`;

            div.addEventListener("click", function () {
              addSelectedUser(user);
              personeelInput.value = "";
              personeelSuggestions.style.display = "none";
            });

            div.addEventListener("mouseenter", function () {
              this.style.background = "#f0f0f0";
            });
            div.addEventListener("mouseleave", function () {
              this.style.background = "#fff";
            });

            personeelSuggestions.appendChild(div);
          });
          personeelSuggestions.style.display = "block";
        } else {
          personeelSuggestions.style.display = "none";
        }
      })
      .catch((err) => {
        console.error("Fout bij ophalen gebruikers:", err);
      });
  });

  // Sluit suggestions bij klik buiten
  document.addEventListener("click", function (e) {
    if (e.target !== personeelInput) {
      personeelSuggestions.style.display = "none";
    }
  });
}

function addSelectedUser(user) {
  // Voeg toe aan array
  selectedUsers.push(user);

  // Maak badge
  const badge = document.createElement("span");
  badge.style.background = "#e5dbfa";
  badge.style.color = "#6b5b95";
  badge.style.padding = "4px 8px";
  badge.style.borderRadius = "4px";
  badge.style.fontSize = "14px";
  badge.style.cursor = "pointer";
  badge.dataset.userId = user.user_id;
  badge.textContent = `${user.first_name} ${user.last_name}`;

  badge.addEventListener("click", function () {
    removeSelectedUser(user.user_id);
    badge.remove();
  });

  selectedPersoneelDiv.appendChild(badge);

  // Update hidden input met comma-separated user_id's
  updatePersoneelHidden();
}

function removeSelectedUser(userId) {
  selectedUsers = selectedUsers.filter((u) => u.user_id !== userId);
  updatePersoneelHidden();
}

function updatePersoneelHidden() {
  if (personeelHidden) {
    personeelHidden.value = selectedUsers.map((u) => u.user_id).join(",");
  }
}

// Load task members functie
function loadTaskMembers(slotId) {
  const container = document.querySelector(`.task-members-${slotId}`);
  if (!container) return;

  fetch(`${baseUrl}/api/users/get_assigned_users.php?slot_id=${slotId}`)
    .then((res) => res.json())
    .then((data) => {
      if (data.success && data.assigned && data.assigned.length > 0) {
        const membersList = data.assigned
          .map(
            (user) =>
              `<span style='display:inline-block;background:#e5dbfa;color:#6b5b95;padding:4px 8px;border-radius:4px;font-size:13px;margin:2px;'>${user.first_name} ${user.last_name}</span>`,
          )
          .join("");
        container.innerHTML = `
          <div style='font-weight:500;margin-bottom:4px;'>Ingeschreven leden (${data.assigned.length}):</div>
          <div>${membersList}</div>
        `;
      } else {
        container.innerHTML = `
          <div style='font-weight:500;margin-bottom:4px;'>Ingeschreven leden:</div>
          <div style='color:#999;font-style:italic;'>Nog niemand ingeschreven</div>
        `;
      }
    })
    .catch((err) => {
      console.error("Error loading task members:", err);
      container.innerHTML = `
        <div style='font-weight:500;margin-bottom:4px;'>Ingeschreven leden:</div>
        <div style='color:#dc3545;'>Fout bij laden</div>
      `;
    });
}

// Handle signup/signout toggle functie
function handleSignupToggle(slotId, isSignedUp) {
  if (isSignedUp) {
    // Uitschrijven
    if (!confirm("Weet je zeker dat je je wilt uitschrijven van deze taak?")) {
      return;
    }

    const formData = new FormData();
    formData.append("slot_id", slotId);

    fetch(`${baseUrl}/api/tasks/signout_task.php`, {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          // Sluit modal en herlaad
          const modal = document.getElementById("tasksModal");
          if (modal) modal.style.display = "none";
          alert(data.message);
          regenerateCalendar();
          regenerateWeekView();
        } else {
          alert("Fout: " + data.message);
        }
      })
      .catch((err) => {
        console.error("Signout error:", err);
        alert("Er ging iets fout bij het uitschrijven");
      });
  } else {
    // Inschrijven
    const formData = new FormData();
    formData.append("slot_id", slotId);

    fetch(`${baseUrl}/api/tasks/signup_task.php`, {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((data) => {
        if (data.success) {
          // Sluit modal en herlaad
          const modal = document.getElementById("tasksModal");
          if (modal) modal.style.display = "none";
          alert(data.message);
          regenerateCalendar();
          regenerateWeekView();
        } else {
          alert("Fout: " + data.message);
        }
      })
      .catch((err) => {
        console.error("Signup error:", err);
        alert("Er ging iets fong bij het inschrijven");
      });
  }
}

// Initialize with current month

// Init week view direct op juiste week en update info
document.addEventListener("DOMContentLoaded", function () {
  updateCalendarTitle();
  regenerateCalendar(); // 👈 deze ontbrak
  updateWeekInfo();
  regenerateWeekView();
});

console.log("Agenda pagina geladen!");
