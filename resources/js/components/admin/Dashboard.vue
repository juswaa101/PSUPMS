<template>
    <div>
        <!-- Sidebar -->
        <div :class="{sidebar: isSidebar}">
            <div class="logo-details">
                <i><img :src="'/assets/login/psu.png'" alt="logo" class="imgs" height="61" width="55"></i>
                <span class="logo_name">PMS</span>
            </div>


            <ul class="nav-links">
                <!-- Dashboard -->
                <li>
                    <a href="/admin/dashboard">
                        <i class='bx bx-grid-alt'></i>
                        <span class="link_name">Dashboard</span>
                    </a>

                    <!-- hover -->
                    <ul class="sub-menu blank">
                        <li><a class="link_name" href="/admin/dashboard">Dashboard</a></li>
                    </ul>
                </li>

                <!-- User Management -->
                <li>
                    <a href="/admin/user-management">
                        <i class='bx bx-user'></i>
                        <span class="link_name">User Management</span>
                    </a>

                    <!-- hover -->
                    <ul class="sub-menu blank">
                        <li><a class="link_name" href="/admin/user-management">User Management</a></li>
                    </ul>
                </li>


                <!-- Project -->
                <li>
                    <div class="icon-link">
                        <a href="/admin/project">
                            <i class='bx bx-collection'></i>
                            <span class="link_name">Projects</span>
                        </a><i class='bx bxs-chevron-down arrow'></i>
                    </div>

                    <!-- hover -->
                    <ul class="sub-menu">
                        <li><a class="link_name" href="/admin/project/">Projects</a></li>

                        <div v-if="projects !== null">
                            <div v-for="item in projects">
                                <li><a :href="'/admin/project/' + item.project_id">{{ item.project_title }}</a></li>
                            </div>
                        </div>
                    </ul>
                </li>


                <li>
                    <!-- Reports -->
                    <a href="/admin/reports">
                        <i class='bx bx-pie-chart-alt-2'></i>
                        <span class="link_name">Reports</span>
                    </a>

                    <!-- hover -->
                    <ul class="sub-menu blank">
                        <li><a class="link_name" href="/admin/reports">Reports</a></li>
                    </ul>
                </li>

                <!-- Logout -->
                <li>
                    <div class="profile-details">
                        <div class="name-job">
                            <div class="logout"><a href="/logout"><p style="color:white;">Logout</p></a></div>
                        </div>
                        <a href="/logout"><i class='bx bx-log-out'></i></a>
                    </div>
                </li>
            </ul>
        </div>
        <!-- End Sidebar -->

        <section class="home-section">
            <div class="home-content">
                <!-- menu icon -->
                <i class='bx bx-menu'></i>

                <!-- bell icon -->
                <i class='bx bx-bell bx-sm bx-tada-hover bx-border-circle'
                   data-bs-target="#offcanvasNotification" data-bs-toggle="offcanvas"></i><span class="badge bg-danger countBadge" id="countNotification"></span>&nbsp;&nbsp;

                <!-- user icon -->
                <i aria-controls="offcanvasWithBothOptions" class='bx bx-user bx-sm bx-border-circle'
                   data-bs-target="#offcanvasWithBothOptions" data-bs-toggle="offcanvas"></i>
            </div>
            <div class="container mx-auto mt-4 mb-5">
                <div class="row mt-3">
                    <div class="col-md-12">
                        <div class="card">
                            <div class="card-header p-4 text-white card-h">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <p class="fs-1 m-0" v-for="(value, key, index) in item" :key="item.id"
                                               v-if="index < 1">{{ item.project_title }} </p>
                                        </div>
                                        <div class="col-md-4">
                                            <div class="dropdown">
                                                <i aria-expanded="false"
                                                   class="bx bx-dots-horizontal-rounded bx-md pt-3"
                                                   data-bs-toggle="dropdown" type="button"></i>
                                                <ul class="dropdown-menu" style="background-color:#E4E9F7;">
                                                    <li><a class="dropdown-item" data-bs-toggle="offcanvas"
                                                           data-bs-target="#offcanvasViewProject">View Project</a></li>
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li><a class="dropdown-item" @click="editProject(item)">Edit
                                                        Project</a></li>
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li><a class="dropdown-item"
                                                           @click="deleteProject(item.project_title.toString())">Delete
                                                        Project</a></li>
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li><a class="dropdown-item" @click="openBoardColorModal">Change
                                                        Color</a></li>
                                                    <li>
                                                        <hr class="dropdown-divider">
                                                    </li>
                                                    <li><a class="dropdown-item" @click="toggleFinishedProject(item.project_id)">Add to finished projects</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-4">
                                <!-- Project Title -->
                                <nav>
                                    <div id="nav-tab" class="nav nav-tabs" role="tablist">
                                        <button id="nav-home-tab" aria-controls="nav-home" aria-selected="true"
                                                class="nav-link active" data-bs-target="#nav-home"
                                                data-bs-toggle="tab"
                                                role="tab" type="button">Column
                                        </button>
                                        <button id="nav-profile-tab" aria-controls="nav-profile"
                                                aria-selected="false"
                                                class="nav-link" data-bs-target="#nav-profile" data-bs-toggle="tab"
                                                role="tab" type="button">List
                                        </button>
                                        <button id="nav-profile-tab" aria-controls="nav-profile"
                                                aria-selected="false"
                                                class="nav-link" data-bs-target="#offcanvasAddBoard" data-bs-toggle="offcanvas"
                                                role="tab" type="button">Create Column
                                        </button>
                                        <button id="nav-profile-tab" aria-controls="nav-profile"
                                                aria-selected="false"
                                                class="nav-link" data-bs-target="#offcanvasAddTask" data-bs-toggle="offcanvas"
                                                role="tab" type="button">Create Task
                                        </button>
                                    </div>
                                </nav>
                                <br>

                                <div class="container">
                                    <div class="row">
                                        <div class="col-md my-2">
                                            <label  data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUsersModal">Add Members</label>
                                            <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="ms-3 bi bi-plus-circle-fill" viewBox="0 0 16 16" data-bs-toggle="offcanvas" data-bs-target="#offcanvasAddUsersModal">
                                                <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zM8.5 4.5a.5.5 0 0 0-1 0v3h-3a.5.5 0 0 0 0 1h3v3a.5.5 0 0 0 1 0v-3h3a.5.5 0 0 0 0-1h-3v-3z"/>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                <div id="nav-tabContent" class="tab-content">
                                    <div id="nav-home" aria-labelledby="nav-home-tab"
                                         class="tab-pane fade show active"
                                         role="tabpanel">
                                        <div id="chartDiv" style="max-width: 840px; min-width:330px; height: 440px;margin: 0 auto">
                                        </div>
                                        <div class="container-fluid" v-if="boards.length !== 0">
                                            <main class="flexbox py-4">
                                                <div class="row">
                                                    <div class="col text-uppercase" v-for="board in boards"
                                                         v-bind:key="board.id">
                                                        <div :id="board.id">
                                                            <div class="row align-items-start">
                                                                <div class="col">
                                                                    <h5 id="title-text">{{ board.name }} - {{ board.board_progress.total_task_done }} / {{ board.board_progress.total_task }}</h5>
                                                                </div>
                                                                <div class="col">
                                                                    <div class="dropdown">
                                                                        <i class="bx bx-dots-horizontal-rounded bx-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false"></i>
                                                                        <ul class="dropdown-menu" style="background-color:#E4E9F7;">
                                                                            <li><a class="dropdown-item" @click="editBoard(board)">Edit</a></li>
                                                                            <div class="dropdown-divider"></div>
                                                                            <li><a class="dropdown-item" @click="deleteBoard(board.id)">Delete</a></li>
                                                                        </ul>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="row">
                                                                <div class="col">
                                                                    <div class="progress">
                                                                        <div class="progress-bar bg-success text-light display-6 fs-6" 
                                                                            :style="{ 'width' : (board.board_progress.total_task_done/board.board_done.total_task)*100 + '%' }" 
                                                                            role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                                                            {{ 
                                                                                Number.isNaN(Math.floor((board.board_progress.total_task_done/board.board_progress.total_task)*100)) ? 0 + '%' :
                                                                                Math.floor((board.board_progress.total_task_done/board.board_progress.total_task)*100) + '%' 
                                                                            }}
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <br>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col" v-for="board in boards" v-bind:key="board.id">
                                                        <Board :id="board.id" :style="('backgroundColor:'+currentBoardColor)">
                                                            <div v-for="task in tasks" v-bind:key-="task.id">
                                                                <div v-if="board.id === task.board_id">
                                                                    <Task :id="task.id" draggable="true">
                                                                        <div class="card shadow-sm mt-2" :style="('backgroundColor:'+currentTaskColor)">
                                                                            <div class="card-body">
                                                                                <div class="col">
                                                                                    <div class="progress">
                                                                                        <div class="progress-bar bg-success text-light display-6 fs-6" 
                                                                                            :style="{ 'width' : (task.total_subtask_done.total_subtask_done/task.total_subtask.total_subtask)*100 + '%' }" 
                                                                                            role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                                                                            {{ 
                                                                                                Number.isNaN(Math.floor((task.total_subtask_done.total_subtask_done/task.total_subtask.total_subtask)*100)) ? 0 + '%' :
                                                                                                Math.floor((task.total_subtask_done.total_subtask_done/task.total_subtask.total_subtask)*100) + '%' 
                                                                                            }}
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <div class="dropdown text-end">
                                                                                    <i class="bx bx-dots-horizontal-rounded bx-md" type="button" data-bs-toggle="dropdown" aria-expanded="false"></i>
                                                                                    <ul class="dropdown-menu" style="background-color:#E4E9F7;">
                                                                                        <li><a @click="showModal(task)" class="dropdown-item">View</a></li>
                                                                                        <div class="dropdown-divider"></div>
                                                                                        <li><a @click="editTask(task)" class="dropdown-item">Edit</a></li>
                                                                                        <div class="dropdown-divider"></div>
                                                                                        <li><a @click="assignUserTaskModal(task.id)" class="dropdown-item">Assign Members To Task</a></li>
                                                                                        <div class="dropdown-divider"></div>
                                                                                        <li><a @click="deleteTask(task.id)" class="dropdown-item">Delete</a></li>
                                                                                        <div class="dropdown-divider"></div>
                                                                                        <li><a class="dropdown-item" @click="openTaskColorModal">Change Color</a></li>
                                                                                    </ul>
                                                                                </div>
                                                                                <div class="card-title">
                                                                                    <div class="container">
                                                                                        <div class="row">
                                                                                            <h5 class="text-primary">{{ task.name.substring(0, 30) + "..." }}</h5>
                                                                                        </div>
                                                                                    </div>
                                                                                </div>
                                                                                <p class="ps-4">
                                                                                    {{ task.description.substring(0, 50) + "..." }}
                                                                                </p>
                                                                            </div>
                                                                        </div>
                                                                    </Task>
                                                                </div>
                                                            </div>
                                                        </Board>
                                                    </div>
                                                </div>
                                            </main>
                                        </div>
                                        <div v-else>
                                            <div class="container-fluid mt-4">
                                                <h1 class="display-4 fs-4">No Columns and Task Yet!</h1>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="nav-profile" aria-labelledby="nav-profile-tab" class="tab-pane fade"
                                         role="tabpanel">
                                            <div class="box">
                                                <ul class="list-unstyled" id="page_list">
                                                    <div v-for="board in boards">
                                                        <li :id="board.id">
                                                            <div class="container">
                                                                <p class="fw-bold">{{ board.name }}</p>
                                                                <div class="container">
                                                                    <table class="table table-bordered table-striped">
                                                                        <thead>
                                                                        <tr>
                                                                            <th scope="col">Task</th>
                                                                            <th scope="col">Progress</th>
                                                                            <th scope="col">Due Date</th>
                                                                        </tr>
                                                                        <tr v-for="task in tasks" v-bind:key="task.id" v-if="board.id === task.board_id">
                                                                            <th scope="col">{{ task.name }}</th>
                                                                            <th scope="col">
                                                                                <div class="progress">
                                                                                    <div class="progress-bar bg-success text-light display-6 fs-6" 
                                                                                    :style="{ 'width' : (task.total_subtask_done.total_subtask_done/task.total_subtask.total_subtask)*100 + '%' }" 
                                                                                    role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                                                                        {{ 
                                                                                            Number.isNaN(Math.floor((task.total_subtask_done.total_subtask_done/task.total_subtask.total_subtask)*100)) ? 0 + '%' :
                                                                                            Math.floor((task.total_subtask_done.total_subtask_done/task.total_subtask.total_subtask)*100) + '%' 
                                                                                        }}
                                                                                    </div>
                                                                                </div>
                                                                            </th>
                                                                            <th scope="col" v-if="task.task_due_date">{{ task.task_due_date }}</th>
                                                                            <th scope="col" v-else><p class="text text-danger">No Due Date Yet</p></th>
                                                                        </tr>
                                                                        </thead>
                                                                    </table>
                                                                </div>
                                                            </div>
                                                        </li>
                                                    </div>
                                                </ul>
                                                <input type="hidden" name="page_order_list" id="page_order_list" />
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </section>

        <!-- Offcanvas -->
        <div id="offcanvasWithBothOptions" aria-labelledby="offcanvasWithBothOptionsLabel"
             class="offcanvas offcanvas-end" data-bs-scroll="true"
             tabindex="-1">
            <div class="offcanvas-header text-white" style="background-color: #00305F;">
                <h4 id="offcanvasWithBothOptionsLabel" class="offcanvas-title">Profile</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
            </div>
            <div class="offcanvas-body" style="background-color: #E4E9F7;">
                <div class="row">
                    <div class="form-group">
                        <div style="text-align: center;">
                            <img :src="'/assets/login/psu.png'" alt="Profile" class="img-responsive mt-3" height="180"
                                 style="border-radius: 500px;"
                                 width="180"
                            >
                            <div class="form-group mt-3">
                                <p><b style="color: #0a53be"></b></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="ps-2 row text">
                    <div v-for="(value, key, index) in logged" v-if="index < 1">
                        <div class="row text-start mt-3">
                            <div class="col-md-1">
                                <i class='bx bx-user-pin bx-sm'></i>
                            </div>
                            <div class="col-md-11">
                                <h6 class="fw-bold">{{ logged.name }}</h6>
                            </div>
                        </div>

                        <div class="row text-start mt-3">
                            <div class="col-md-1">
                                <i class='bx bx-user-circle bx-sm'></i>
                            </div>
                            <div class="col-md-11">
                                <h6 class="fw-bold">{{ logged.username }}</h6>
                            </div>
                        </div>

                        <div class="row text-start mt-3">
                            <div class="col-md-1">
                                <i class='bx bx-envelope bx-sm'></i>
                            </div>
                            <div class="col-md-11">
                                <h6 class="fw-bold">{{ logged.email }}</h6>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
        <div id="offcanvasNotification" aria-labelledby="offcanvasWithBothOptionsLabel"
             class="offcanvas offcanvas-end" data-bs-scroll="true"
             tabindex="-1">
            <div class="offcanvas-header text-white" style="background-color: #00305F;">
                <h4 id="offcanvasWithBothOptionsLabel" class="offcanvas-title">Notifications</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
            </div>
            <div class="offcanvas-body" style="background-color: #E4E9F7;">
                <div v-if="notification.length !== 0">
                    <a :href="'/read-all/notification'" class="text text-primary">Mark as all read</a>
                    <div v-for="notify in notification">
                        <div class="card notification-card notification-invitation mb-2 mt-2">
                            <div class="card-body">
                                <table>
                                    <tr>
                                        <td>
                                            <div v-if="notify.image !== null">
                                                <img class="rounded-circle shadow-4 float-start" :src="'/assets/users/' + notify.image "
                                                     alt="user_image" height="50px" width="50px" style="margin-right: 10px;">
                                                <div class="card-title fw-bold">{{ notify.name }}</div>
                                                <div class="card-title">{{ notify.notification_message }}</div>
                                            </div>
                                            <div v-else>
                                                <img class="rounded-circle shadow-4 float-start" :src="'/assets/login/psu.png'"
                                                     alt="user_image" height="50px" width="50px" style="margin-right: 10px;">
                                                <div class="card-title fw-bold">{{ notify.name }}</div>
                                                <div class="card-title">{{ notify.notification_message }}</div>
                                            </div>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width:30%" class="mt-2">
                                            <div class="row">
                                                <div class="col-md-6" v-if="notify.has_read === 0">
                                                    <a :href="'/read/notification/' + notify.notify_id" class="btn btn-primary w-100">Mark as Read</a>
                                                </div>
                                                <div class="col-md-6" v-else>
                                                    <a :href="'/unread/notification/' + notify.notify_id" class="btn btn-secondary w-100">Mark Unread</a>
                                                </div>
                                                <div class="col-md-6">
                                                    <a :href="'/delete/notification/' + notify.notify_id" class="btn btn-danger w-100">Dismiss</a>
                                                </div>
                                            </div>
                                            <p class="text-muted float-end mt-3">{{ notify.created }}</p>
                                        </td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="container p-3" v-else>
                    <h4 class="text-center">No Notifications Yet</h4>
                </div>
            </div>
        </div>
        <div id="offcanvasAddTask" aria-labelledby="offcanvasWithBothOptionsLabel"
             class="offcanvas offcanvas-end" data-bs-scroll="true"
             tabindex="-1">
            <div class="offcanvas-header text-white" style="background-color: #00305F;">
                <h4 id="offcanvasWithBothOptionsLabel" class="offcanvas-title">Create Task</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
            </div>
            <div class="offcanvas-body" style="background-color: #E4E9F7;">
                <div class="container-fluid">
                    <form @submit.prevent="addTask">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <div class="form-group mt-2">
                                        <label for="task-name">
                                            <span class="h5">Board Name</span>
                                        </label>
                                        <select class="form-select mb-3" aria-label=".form-select-lg example" v-model="task.board_id" @change="validationTaskError.board_id ? validationTaskError.board_id = null : null" required>
                                            <option value disabled>Select a Board</option>
                                            <option
                                                v-for="board in boards"
                                                v-bind:key="board.id"
                                                :value="board.id"
                                            >{{ board.name }}</option>
                                        </select>
                                        <span v-if="validationTaskError.board_id" class="text text-danger">{{ validationTaskError.board_id[0] }}</span>
                                    </div>
                                    <div class="form-group mt-2">
                                        <label for="task-name">
                                            <span class="h5">Task Name</span>
                                        </label>
                                        <input type="text" class="form-control" v-model="task.name" @keyup="validationTaskError.name ? validationTaskError.name = null : null" placeholder="Task Name" required/>
                                        <span v-if="validationTaskError.name" class="text text-danger">{{ validationTaskError.name[0] }}</span>
                                    </div>
                                    <div class="form-group mt-2">
                                        <label for="task-decription">
                                            <span class="h5">Task Description</span>
                                        </label>
                                        <textarea class="form-control" v-model="task.description" @keyup="validationTaskError.description ? validationTaskError.description = null : null" placeholder="Task Description" required></textarea>
                                        <span v-if="validationTaskError.description" class="text text-danger">{{ validationTaskError.description[0] }}</span>
                                    </div>
                                    <div class="form-group mt-2">
                                        <label for="task-status">
                                            <span class="h5">Status</span>
                                        </label>
                                        <select v-model="task.privacy_status" class="form-select" @change="validationTaskError.privacy_status ? validationTaskError.privacy_status = null : null" required>
                                            <option value disabled selected>Select Task Privacy</option>
                                            <option value=0>Public</option>
                                            <option value=1>Private</option>
                                        </select>
                                        <span v-if="validationTaskError.privacy_status" class="text text-danger">{{ validationTaskError.privacy_status[0] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary float-end mt-2">Add</button>
                    </form>
                </div>
            </div>
        </div>
        <div id="offcanvasAddBoard" aria-labelledby="offcanvasWithBothOptionsLabel"
             class="offcanvas offcanvas-end" data-bs-scroll="true"
             tabindex="-1">
            <div class="offcanvas-header text-white" style="background-color: #00305F;">
                <h4 id="offcanvasWithBothOptionsLabel" class="offcanvas-title">Create Column</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
            </div>
            <div class="offcanvas-body" style="background-color: #E4E9F7;">
                <div class="container-fluid">
                    <form @submit.prevent="addBoard">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label class="form-label" for="board_name">Column Name</label>
                                    <input type="text" class="form-control" v-model="board.name" @keyup="validationBoardError = null" placeholder="Column Name" required>
                                    <div v-for="errorArray in validationBoardError" :key="errorArray.id">
                                                <span class="text text-danger mt-3">{{ errorArray.toString().replace('[', '')
                                                    .replace(']', '').replace('"', '') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary mt-2 float-end">Add</button>
                    </form>
                </div>
            </div>
        </div>
        <div id="offcanvasViewProject" aria-labelledby="offcanvasWithBothOptionsLabel"
             class="offcanvas offcanvas-end" data-bs-scroll="true"
             tabindex="-1">
            <div class="offcanvas-header text-white" style="background-color: #00305F;">
                <h4 id="offcanvasWithBothOptionsLabel" class="offcanvas-title">View Project</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
            </div>
            <div class="offcanvas-body" style="background-color: #E4E9F7;">
                <div class="container-fluid">
                    <div :key="fetch.id" v-for="(value, key, index) in fetch">
                        <div class="row">
                            <div class="col-md-12">
                                <h3>Project Title: {{ value.project_title }}</h3>
                            </div>
                            <div class="col-md-12 mt-2">
                                <p>{{ value.project_description }}</p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <p>Project Start Date: {{ value.project_start_date }}</p>
                            </div>
                            <div class="col-md-12">
                                <p>Project Due Date: {{ value.project_end_date }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <p>Project Head:</p>
                            <div :key="key" :value="value" v-for="(value, key, index) in head">
                                <li class="mx-3">{{ value.name }}</li>
                            </div>
                        </div>
                        <div class="col-md-12 mt-3">
                            <p>Project Members:</p>
                            <div v-if="staff.length != 0">
                                <div :key="key" :value="value" v-for="(value, key, index) in staff">
                                    <li class="mx-3">{{ value.name }}</li>
                                </div>
                            </div>
                            <div v-else>
                                <li>No Project Members Yet!</li>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="offcanvasAddUsersModal" aria-labelledby="offcanvasWithBothOptionsLabel"
             class="offcanvas offcanvas-end" data-bs-scroll="true"
             tabindex="-1">
            <div class="offcanvas-header text-white" style="background-color: #00305F;">
                <h4 id="offcanvasWithBothOptionsLabel" class="offcanvas-title">Add/Remove Members in Project</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
            </div>
            <div class="offcanvas-body" style="background-color: #E4E9F7;">
                <div class="container-fluid">
                    <div v-for="(errorArray, idx) in validationMemberError" :key="idx">
                        <div v-for="(allErrors, idx) in errorArray" :key="idx">
                            <span class="text-danger">{{ allErrors }} </span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="head">Members:</label>
                                    <select class="form-control" id="selectMembers" style="width: 100%;" v-model="formMembers.members" multiple required>
                                        <option v-for="user in users" :value="user.id" v-if="user_head.user_id !== user.id">
                                            {{ user.name }}
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    <button class="btn btn-warning mt-3 isDisabled" @click="updateMembers()">UPDATE</button>
                    <button class="btn btn-secondary mt-3" data-bs-dismiss="offcanvas">CANCEL</button>
                </div>
            </div>
        </div>
        <div id="offcanvasEditProjectModal" aria-labelledby="offcanvasWithBothOptionsLabel"
             class="offcanvas offcanvas-end" data-bs-scroll="true"
             tabindex="-1">
            <div class="offcanvas-header text-white" style="background-color: #00305F;">
                <h4 id="offcanvasWithBothOptionsLabel" class="offcanvas-title">Update Project</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
            </div>
            <div class="offcanvas-body" style="background-color: #E4E9F7;">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label" for="title">Project Title:</label>
                            <input type="text" name="project_title" id="project_title" class="form-control"
                                   placeholder="Project Title" v-model="formEdit.project_title"
                                   @keyup="validationUpdateProjectError.project_title ? validationUpdateProjectError.project_title = null : null"
                                   required
                            >
                            <span class="text text-danger" v-if="validationUpdateProjectError.project_title">{{ validationUpdateProjectError.project_title[0] }}</span>
                        </div>
                        <div class="col-md-12 mt-2">
                            <label class="form-label" for="description">Project Description:</label>
                            <textarea name="project_description" id="project_description" rows="5"
                                      class="form-control" placeholder="Project Description"
                                      v-model="formEdit.project_description"
                                      @keyup="validationUpdateProjectError.project_description ? validationUpdateProjectError.project_description = null : null"
                                      required
                            ></textarea>
                            <span class="text text-danger" v-if="validationUpdateProjectError.project_description">{{ validationUpdateProjectError.project_description[0] }}</span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label class="form-label" for="">Project Start Date:</label>
                            <input type="date" name="project_start_date" id="project_start_date"
                                   class="form-control" v-model="formEdit.project_start_date" 
                                   :min="item.project_start_date"
                                   disabled
                                   @change="validationUpdateProjectError.project_start_date ? validationUpdateProjectError.project_start_date = null : null"
                                   required       
                            >
                            <span class="text text-danger" v-if="validationUpdateProjectError.project_start_date">{{ validationUpdateProjectError.project_start_date[0] }}</span>
                        </div>
                        <div class="col-md-12 mt-2">
                            <label class="form-label" for="description">Project End Date:</label>
                            <input type="date" name="project_end_date" id="project_end_date"
                                   class="form-control" v-model="formEdit.project_end_date"
                                   :min="item.project_end_date"
                                   @change="validationUpdateProjectError.project_end_date ? validationUpdateProjectError.project_end_date = null : null"
                                   required       
                            >
                            <span class="text text-danger" v-if="validationUpdateProjectError.project_end_date">{{ validationUpdateProjectError.project_end_date[0] }}</span>
                        </div>
                    </div>
                    <button class="btn btn-warning mt-2 float-end" @click="updateProject()">YES</button>
                    <button class="btn btn-secondary mt-2 mx-2 float-end" data-bs-dismiss="offcanvas">CANCEL</button>
                </div>
            </div>
        </div>
        <div id="offcanvasBoardChangeColor" aria-labelledby="offcanvasWithBothOptionsLabel"
             class="offcanvas offcanvas-end" data-bs-scroll="true"
             tabindex="-1">
            <div class="offcanvas-header text-white" style="background-color: #00305F;">
                <h4 id="offcanvasWithBothOptionsLabel" class="offcanvas-title">Customize Board Color</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
            </div>
            <div class="offcanvas-body" style="background-color: #E4E9F7;">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-4 mt-2" v-for="color in colors" :key="color">
                            <div class="color">
                                <div :style="'backgroundColor:' + color" @click="changeBoardColor(color)"
                                     style="padding: 10px;">
                                    <p style="color: black; text-align: center">{{ color }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="offcanvasTaskChangeColor" aria-labelledby="offcanvasWithBothOptionsLabel"
             class="offcanvas offcanvas-end" data-bs-scroll="true"
             tabindex="-1">
            <div class="offcanvas-header text-white" style="background-color: #00305F;">
                <h4 id="offcanvasWithBothOptionsLabel" class="offcanvas-title">Customize Task Color</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
            </div>
            <div class="offcanvas-body" style="background-color: #E4E9F7;">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-4 mt-2" v-for="color in colors" :key="color">
                            <div class="color">
                                <div :style="'backgroundColor:' + color" @click="changeTaskColor(color)"
                                     style="padding: 10px;">
                                    <p style="color: black; text-align: center">{{ color }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="offcanvasViewTask" aria-labelledby="offcanvasWithBothOptionsLabel"
             class="offcanvas offcanvas-end w-50" data-bs-scroll="true"
             tabindex="-1">
            <div class="offcanvas-header text-white" style="background-color: #00305F;">
                <div class="row">
                    <div class="col-md-12">
                        <h4 id="offcanvasWithBothOptionsLabel" class="offcanvas-title">
                            {{ show.name }}
                        </h4>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
            </div>
            <div class="offcanvas-body" style="background-color: #E4E9F7;">
                <div class="container-fluid">
                    <p>{{ show.description }}</p>
                    <div class="form-group">
                        <label for="" class="form-label">Assigned Members: </label>
                        <div v-if="user_assigned_task.length === 0">
                            <p class="text text-danger">No Assigned Members Yet!</p>
                        </div>
                        <div v-else>
                            <li v-for="assigned_members in user_assigned_task">{{ assigned_members.full_name }}</li>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="form-label mt-2">Start Date:</label>
                        <p v-if="show.task_start_date != null">{{ show.task_start_date }}</p>
                        <p v-else class="text text-danger">No Assigned Task Start Date</p>
                    </div>
                    <div class="form-group">
                        <label for="" class="form-label mt-2">Due date:</label>
                        <p v-if="show.task_due_date != null">{{ show.task_due_date }}</p>
                        <p v-else class="text text-danger">No Assigned Task Start Date</p>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <h3>Subtask:</h3>
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-secondary float-end" title="Toggle File Upload" style="margin-left:10px;" @click="toggleUpload = !toggleUpload"><i class="bi bi-paperclip"></i></button>
                                <div class="fs-1 m-0" v-for="(value, key, index) in item" :key="item.id"
                                     v-if="index < 1">
                                    <!--                                            <div v-if="item.create_subtask_status !== 0">-->
                                    <button class="btn btn-success float-end" title="Toggle Subtask Board" @click="toggleSubtaskBoard = !toggleSubtaskBoard"><i class="bi bi-kanban"></i></button>
                                    <!--                                            </div>-->
                                </div>
                            </div>
                            <div class="col-md-12" v-show="toggleSubtaskBoard">
                                <div class="col-md-12 mt-2">
                                    <label class="form-label" for="Subtask Name">Subtask Name:</label>
                                    <input type="text" class="form-control" placeholder="Subtask Name" v-model="formSubtask.subtask_name" required
                                           @keyup="validationSubtaskError.subtask_name ? validationSubtaskError.subtask_name = null : null"
                                    >
                                    <span class="text text-danger" v-if="validationSubtaskError.subtask_name">{{ validationSubtaskError.subtask_name[0] }}</span>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <label class="form-label" for="Subtask Description">Subtask Description: </label>
                                    <textarea class="form-control" rows="3" placeholder="Subtask Description" v-model="formSubtask.subtask_description" required
                                              @keyup="validationSubtaskError.subtask_description ? validationSubtaskError.subtask_description = null : null"></textarea>
                                    <span class="text text-danger" v-if="validationSubtaskError.subtask_description">{{ validationSubtaskError.subtask_description[0] }}</span>
                                </div>
                                <div class="col-md-12 mt-2">
                                    <button class="btn btn-primary" @click="addSubtask()">Add / Update <i class="bi bi-plus"></i></button>
                                </div>
                            </div>
                            <div class="col-md-12" v-show="toggleSubtaskBoard">
                                <div class="container-fluid pt-3">
                                    <main class="flexbox py-2">
                                        <div class="row">
                                            <div class="col-4 text-uppercase" v-for="subtask in subtask_board_name">
                                                <div class="col-4 text-uppercase">{{ subtask.name }}</div>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <div class="col-4" v-for="(item, index) in subtask_board_name">
                                                <input type="hidden" :value="show.id" name="task_id">
                                                <SubtaskBoard :id="index">
                                                    <div v-for="subtask in subtasks" v-bind:key="subtask.id">
                                                        <div v-if="index === subtask.board_id">
                                                            <Subtask :id="subtask.id" draggable="true">
                                                                <div class="card shadow-sm mt-2">
                                                                    <div class="card-body">
                                                                        <div class="dropdown text-end">
                                                                            <i class="bx bx-dots-horizontal-rounded bx-md" type="button" data-bs-toggle="dropdown" aria-expanded="false"></i>
                                                                            <ul class="dropdown-menu" style="background-color:#E4E9F7;">
                                                                                <li><a @click="editSubtask(subtask)" class="dropdown-item">Edit</a></li>
                                                                                <div class="dropdown-divider"></div>
                                                                                <li><a @click="deleteSubtask(subtask.id)" class="dropdown-item">Delete</a></li>
                                                                            </ul>
                                                                        </div>
                                                                        <div class="card-title">
                                                                            <h3 class="lead font-weight-light text-primary">{{ subtask.subtask_name }}</h3>
                                                                        </div>
                                                                        <p>
                                                                            {{ subtask.subtask_description?.toString().substring(0, 30) }}
                                                                        </p>
                                                                    </div>
                                                                </div>
                                                            </Subtask>
                                                        </div>
                                                    </div>
                                                </SubtaskBoard>
                                            </div>
                                        </div>
                                    </main>
                                </div>
                            </div>
                            <div class="col-md-12" v-show="toggleUpload">
                                <input type="file" class="mt-2" id="upload_file" name="upload_file">
                            </div>
                            <div class="col-md-12 mt-2">
                                <div class="row">
                                    <div v-for="attachments in attachment">
                                        <div class="card mt-2">
                                            <div class="card-footer">
                                                <div class="row">
                                                    <div class="col-md-10">
                                                        <p class="text-muted">{{ attachments.filepath }}</p>
                                                        <div class="row">
                                                            <div class="col-md-4">
                                                                <small><span class="bi-calendar-date text-muted"> {{ attachments.created_at.toString().substring(0, 10) }}</span></small>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <button type="button" class="btn btn-success float-end" @click="downloadFile(attachments.filepath)">
                                                                    <i class="bi bi-download"></i>
                                                                </button>
                                                                <button type="button" class="btn btn-danger float-end mx-2" @click="removeFile(attachments.id, attachments.filepath)">
                                                                    <i class="bi bi-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-2">
                                                        <i class="bi bi-file-earmark-text" style="font-size: 30px"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mt-3">
                                <h3 id="comment_scroll">Comments: </h3>
                                <div class="container mt-3">
                                    <div v-for="comment in comments">
                                        <div class="card mt-2">
                                            <div class="row p-2">
                                                <div class="col-md-2">
                                                    <div style="text-align: center;">
                                                        <img class="rounded-circle"
                                                             :src="comment.image === null ? '/assets/login/psu.png' : '/assets/users/' + comment.image"
                                                             width="50" alt="user_image">
                                                    </div>
                                                </div>
                                                <div class="col-md-8">
                                                    <span class="d-block font-weight-bold name">{{ comment.name }}</span>
                                                    <span class="date text-black-50">{{ comment.date_created }}</span>
                                                </div>
                                                <div class="col-md-2" v-if="comment.user_id === logged.id">
                                                    <div class="dropdown">
                                                        <i aria-expanded="false"
                                                           class="bx bx-dots-horizontal-rounded bx-sm pt-3"
                                                           data-bs-toggle="dropdown" type="button"></i>
                                                        <ul class="dropdown-menu comment-option" style="background-color:#E4E9F7;">
                                                            <li><a class="dropdown-item" @click="editComment(comment)">Edit Comment</a></li>
                                                            <li>
                                                                <hr class="dropdown-divider">
                                                            </li>
                                                            <li><a class="dropdown-item"
                                                                   @click="deleteComment(comment.id)">Delete
                                                                Comment
                                                            </a></li>
                                                        </ul>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row p-2">
                                                <div class="col-md-12">
                                                    <p class="comment-text">{{ comment.comment }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="offcanvas-bottom sticky-bottom p-2"  style="background-color: #00305F;">
                <div class="input-group">
                            <textarea class="form-control" id="commentArea"
                                      style="resize: none;" v-model="formComment.comment"></textarea>
                    <button class="input-group-addon btn btn-primary" @click="addOrUpdateComment()"><i class="bi bi-send"></i></button>
                </div>
            </div>
        </div>
        <div id="offcanvasEditTask" aria-labelledby="offcanvasWithBothOptionsLabel"
             class="offcanvas offcanvas-end" data-bs-scroll="true"
             tabindex="-1">
            <div class="offcanvas-header text-white" style="background-color: #00305F;">
                <h4 id="offcanvasWithBothOptionsLabel" class="offcanvas-title">Edit Task</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
            </div>
            <div class="offcanvas-body" style="background-color: #E4E9F7;">
                <div class="container-fluid">
                    <form @submit.prevent="updateTask">
                            <div class="form-group">
                                <h5 class="modal-title">Task Name:</h5>
                                <input type="text" class="form-control" v-model="taskEdit.name" required>
                            </div>
                            <div class="form-group">
                                <label for="" class="form-label">Description: </label>
                                <textarea class="form-control" name="description" rows="5" style="resize: none;"
                                          v-model="taskEdit.description" required>{{ taskEdit.description }}</textarea>
                            </div>
                            <div class="form-group">
                                <label for="" class="form-label mt-2">Task start date:</label>
                                <input type="date" class="form-control" v-model="taskEdit.task_start_date" id="task_start_date"
                                    :min="item.project_start_date" :max="item.project_end_date"
                                    required
                                >
                            </div>
                            <div class="form-group">
                                <label for="" class="form-label mt-2">Task due date:</label>
                                <input type="date" class="form-control" v-model="taskEdit.task_due_date" id="task_due_date"
                                    :min="item.project_start_date" :max="item.project_end_date"
                                    required
                                >
                            </div>
                            <div class="form-group">
                                <label class="form-label mt-2" for="privacy">Task Privacy: </label>
                                <select v-model="taskEdit.privacy_status" class="form-select" required>
                                    <option disabled>Select Task Privacy</option>
                                    <option value=0>Public</option>
                                    <option value=1>Private</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-warning mt-2 float-end">Update</button>
                            <button type="button" class="btn btn-secondary mt-2 mx-2 float-end" data-bs-dismiss="offcanvas">Close</button>
                    </form>
                </div>
            </div>
        </div>
        <div id="offcanvasEditBoard" aria-labelledby="offcanvasWithBothOptionsLabel"
             class="offcanvas offcanvas-end" data-bs-scroll="true"
             tabindex="-1">
            <div class="offcanvas-header text-white" style="background-color: #00305F;">
                <h4 id="offcanvasWithBothOptionsLabel" class="offcanvas-title">Edit Board</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
            </div>
            <div class="offcanvas-body" style="background-color: #E4E9F7;">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <table class="table table-primary table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Column Position</th>
                                            <th>Column Name</th>
                                        </tr>
                                    </thead>
                                    <tbody id="board_list">
                                        <tr v-for="(board, index) in boards" :id="board.id">
                                            <th scope="col">{{ index+1 }}</th>
                                            <td>{{ board.name }}</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="form-group mt-2">
                                <label class="form-label" for="admin">Column Name:</label>
                                <input type="text" class="form-control" @keyup="validationUpdateBoardError.name ? validationUpdateBoardError.name = null : null" v-model="boardEdit.name" required>
                                <span v-if="validationUpdateBoardError.name" class="text text-danger mt-2">{{ validationUpdateBoardError.name[0] }}</span>
                            </div>
                            <button class="btn btn-warning mt-2 float-end" @click="updateBoard()">UPDATE</button>
                            <button class="btn btn-secondary mx-2 mt-2 float-end" data-bs-dismiss="offcanvas">CANCEL</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="offcanvasAssignUserToTask" aria-labelledby="offcanvasWithBothOptionsLabel"
             class="offcanvas offcanvas-end" data-bs-scroll="true"
             tabindex="-1">
            <div class="offcanvas-header text-white" style="background-color: #00305F;">
                <h4 id="offcanvasWithBothOptionsLabel" class="offcanvas-title">Assign Task Members</h4>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                        aria-label="Close"></button>
            </div>
            <div class="offcanvas-body" style="background-color: #E4E9F7;">
                <div class="container-fluid">
                    <div v-for="(errorArray, idx) in validationTaskMemberError" :key="idx">
                        <div v-for="(allErrors, idx) in errorArray" :key="idx">
                            <span class="text-danger">{{ allErrors }} </span>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label" for="head">Members:</label>
                                <v-select
                                    v-model="taskMembers.members"
                                    :options="user_assigned"
                                    :getOptionLabel="user => user.name"
                                    :reduce="user => user.id"
                                    :selectable="() => taskMembers.members.length < 20"
                                    multiple
                                    @select="validationTaskMemberError ? validationTaskMemberError = null : null"
                                >
                                </v-select>
                            </div>
                        </div>
                    </div>
                    <button class="btn btn-warning mt-2 float-end" @click="assignMembersTask()">UPDATE</button>
                    <button class="btn btn-secondary mx-2 mt-2 float-end" data-bs-dismiss="offcanvas">CANCEL</button>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import Swal from 'sweetalert2';
import VueCookies from 'vue-cookies';

Vue.prototype.$currentUserArray = [];
Vue.prototype.$currentAssignedTaskMember = [];
Vue.prototype.$boardColumn = [];
Vue.prototype.$taskColumn = [];
Vue.prototype.$boardFinal = [];
Vue.prototype.$boardFinal1 = [];
Vue.prototype.$boardFinal2 = [];

//  get current user's id from meta tag
Vue.prototype.$user_id = document
    .querySelector("meta[name='user-id']")
    .getAttribute("content");

Vue.prototype.$project_id = document
    .querySelector("meta[name='project_id']")
    .getAttribute("content");

export default {
    props: [
        'item', 'users', 'fetch', 'staff', 'head', 'logged',
        'user_assigned', 'user_head', 'notification', 'projects',
        'kanban_task', 'kanban_board_task'
    ],
    data() {
        return {
            toggleUpload: false,
            toggleEditSubtask: false,
            toggleSubtaskBoard: false,
            addOrUpdateSubtask: false,
            taskID:'',
            validationMemberError: '',
            validationUpdateProjectError: '',
            validationTaskUpdateError: '',
            validationBoardError: '',
            validationUpdateBoardError: '',
            validationTaskError: '',
            validationTaskMemberError: '',
            validationSubtaskError: '',
            validationSubtaskUpdateError: '',
            user_assigned_task: [],
            formMembers: {
                members: []
            },
            taskMembers: {
                members: []
            },
            subtask_board_name: [
                { name: 'TO DO' }, { name: 'IN PROGRESS' },
                { name: 'DONE' }
            ],
            show: {
                id: '',
                name: '',
                task_start_date: '',
                task_due_date: '',
                description: '',
                privacy_status: ''
            },
            formEdit: {
                project_title: null,
                project_description: null,
                project_start_date: null,
                project_end_date: null,
            },
            formComment: {
                id: '',
                comment: '',
                is_edit: false,
            },
            formSubtask: {
                id: '',
                board_id: '',
                subtask_name: '',
                subtask_description: '',
            },
            comment_task_id: '',
            comments: [],
            subtasks: [],
            attachment: [],
            form: new FormData,
            boards: [],
            board: {
                id: "",
                board_id: ""
            },
            boardEdit: {
                board_id: "",
                name: "",
            },
            board_id: "",
            tasks: [],
            task: {
                id: "",
                board_id: "",
                name: "",
                description: "",
                privacy_status: null
            },
            taskEdit: {
                id: "",
                board_id: "",
                name: "",
                description: "",
                task_start_date: "",
                task_due_date: "",
                privacy_status: "",
            },
            task_id: "",
            boardIndex: [],
            selectedId: null,
            currentTaskColor: "#FFFFFF",
            currentBoardColor: "#FFFFFF",
            colors: [
                "#F44336", "#E91E63", "#673AB7", "#2196F3",
                "#4CAF50", "#8BC34A", "#FFC107", "#424242",
                "#FFFFFF"
            ],
            isSidebar: true,
            totalSubtask: 0,
            totalSubtaskCompleted: 0,
        };
    },
    created() {
        //  get all boards
        this.fetchBoards();
        //  get all tasks
        this.fetchTasks();

        this.appendMembers();
        
        this.getBoardColor();

        this.getTaskColor();

        this.formMembers.members = this.$currentUserArray;

        this.fetchKanbanTask();

        this.fetchKanbanBoard();
    },
    methods: {
        //  board data handling
        fetchBoards() {
            fetch("/api/boards/" + this.$user_id + "/" + this.$project_id)
                .then(res => res.json())
                .then(res => {
                    this.boards = res.data;
                });
        },
        deleteBoard(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch("/api/board/" + id + "/" + this.$user_id + "/" + this.$project_id, {
                        method: "delete"
                    })
                        .then(res => res.json())
                        .then(data => {
                            Swal.fire(
                                'Board Deleted',
                                'Board Deleted Successfully!',
                                'success'
                            )
                            this.fetchBoards();
                            window.location.reload();
                        })
                        .catch(error => console.log(error));
                }
            })
        },
        addBoard() {
            fetch("/api/board/" + this.$user_id + "/" + this.$project_id, {
                method: "post",
                body: JSON.stringify(this.board),
                headers: {
                    "Content-Type": "application/json"
                }
            })
                .then(res => res.json())
                .then(error => {
                    if (this.board.name == null || this.board.name.length === 0) {
                        this.validationBoardError = error
                    } else {
                        this.validationBoardError = ''
                        if(this.boards.length < 7){
                            Swal.fire({
                                title: 'Board Added',
                                icon: 'success',
                                confirmButtonText: "OK",
                                showConfirmButton: true,
                                allowOutsideClick: false,
                                allowEscapeKey: false
                            }).then((confirm) => {
                                if(confirm.isConfirmed){
                                    this.fetchBoards();
                                    window.location.reload();
                                }
                            })
                        }
                        else {
                            Swal.fire({
                                title: 'Maximum of 7 board per project is allowed',
                                icon: 'error',
                                confirmButtonText: "OK",
                                showConfirmButton: true,
                                allowOutsideClick: false,
                                allowEscapeKey: false
                            });
                        }
                        this.board.name = "";
                    }
                })
                .catch(error => console.log(error));
        },
        editBoard(board) {
            this.boardEdit.board_id = board.id;
            this.boardEdit.index = board.index;
            this.boardEdit.name = board.name;

            let offEdit = $('#offcanvasEditBoard');
            offEdit.offcanvas('show');
        },
        updateBoard() {
            fetch("/api/board/" + this.$user_id + "/" + this.$project_id, {
                method: "put",
                body: JSON.stringify(this.boardEdit),
                headers: {
                    "Content-Type": "application/json"
                }
            })
                .then(res => res.json())
                .then(error => {
                    if (this.boardEdit.name == null || this.boardEdit.name.length === 0) {
                        this.validationUpdateBoardError = error
                        this.validationTaskError = ''
                    } else {
                        this.validationUpdateBoardError = ''
                        Swal.fire({
                            title: 'Board Updated',
                            icon: 'success',
                            confirmButtonText: "OK",
                            showConfirmButton: true,
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        }).then((confirm) => {
                            if(confirm.isConfirmed){
                                this.fetchBoards();
                                window.location.reload();
                            }
                        })
                    }
                })
                .catch(error => console.log(error));
        },

        //  task data handling
        fetchTasks() {
            fetch("/api/tasks/" + this.$user_id + "/" + this.$project_id)
                .then(res => res.json())
                .then(res => {
                    this.tasks = res.data;
                });
        },
        deleteTask(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch("/api/task/" + id + "/" + this.$user_id + "/" + this.$project_id, {
                        method: "delete"
                    })
                        .then(res => res.json())
                        .then(data => {
                            Swal.fire({
                                title: 'Task Deleted',
                                icon: 'success',
                                confirmButtonText: "OK",
                                showConfirmButton: true,
                                allowOutsideClick: false,
                                allowEscapeKey: false
                            }).then((confirm) => {
                                if(confirm.isConfirmed){
                                    this.fetchTasks();
                                    this.fetchBoards();
                                    window.location.reload();
                                }
                            });
                        })
                        .catch(error => console.log(error));
                }
            })
        },
        addTask() {
            fetch("/api/task/" + this.$user_id + "/" + this.$project_id, {
                method: "post",
                body: JSON.stringify(this.task),
                headers: {
                    "Content-Type": "application/json"
                }
            })
                .then(res => res.json())
                .then((response) => {
                    if (this.task.board_id == null || this.task.name == null || this.task.description == null ||
                        this.task.privacy_status == null
                    ) {
                        this.validationTaskError = response
                        console.log(this.validationTaskError);
                    } else {
                        this.validationTaskError = '';
                        this.task.board_id = "";
                        this.task.name = "";
                        this.task.description = "";
                        this.task.privacy_status = null;
                        Swal.fire({
                            title: 'Task Added',
                            icon: 'success',
                            confirmButtonText: "OK",
                            showConfirmButton: true,
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        }).then((confirm) => {
                            if(confirm.isConfirmed) {
                                this.fetchTasks();
                                this.fetchBoards();
                                window.location.reload();
                            }
                        });
                    }
                })
        },
        updateTask() {
            fetch("/api/task/" + this.$user_id + "/" + this.$project_id, {
                method: "put",
                body: JSON.stringify(this.taskEdit),
                headers: {
                    "Content-Type": "application/json"
                }
            })
                .then(res => res.json())
                .then(data => {
                    if (this.taskEdit.board_id == null || this.taskEdit.name == null || this.taskEdit.description == null) {
                        console.log(data)
                        this.validationTaskUpdateError = data
                    } else {
                        this.validationTaskUpdateError = '';
                        Swal.fire({
                            title: 'Task Updated',
                            icon: 'success',
                            confirmButtonText: "OK",
                            showConfirmButton: true,
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        }).then((confirm) => {
                            if (confirm.isConfirmed) {
                                this.fetchTasks();
                                this.fetchBoards();
                                window.location.reload();
                            }
                        });
                    }
                })
                .catch(error => console.log(error));
        },
        editTask(task) {
            $('#offcanvasEditTask').offcanvas('show');
            this.taskEdit.id = task.id;
            this.taskEdit.board_id = task.board_id;
            this.taskEdit.task_id = task.id;
            this.taskEdit.board_name = task.board_name;
            this.taskEdit.name = task.name;
            this.taskEdit.description = task.description;
            this.taskEdit.task_start_date = task.task_start_date;
            this.taskEdit.task_due_date = task.task_due_date;
            this.taskEdit.privacy_status = task.privacy_status;
        },
        assignUserTaskModal(id) {
            this.clearAssignedUser();
            this.assignedUserTask(id);
            $('#offcanvasAssignUserToTask').offcanvas('show');
            this.taskID = id;
        },
        assignedUserTask(id) {
            axios.get('/admin/task/assigned/' + id)
                .then((response) => {
                    this.user_assigned_task = response.data;
                    this.user_assigned_task.forEach(task_person => {
                        this.$currentAssignedTaskMember.push(task_person.user_id);
                    });
                    this.taskMembers.members = this.$currentAssignedTaskMember;
                });

        },
        clearAssignedUser() {
            this.taskMembers.members.splice(0);
            this.user_assigned_task.splice(0);
            this.$currentAssignedTaskMember.splice(0);
        },
        assignMembersTask() {
            axios.put('/admin/task/update-members/' + this.taskID, this.taskMembers)
                .then(() => {
                    Swal.fire({
                        title: 'Task Member assigned!',
                        icon: 'success',
                        confirmButtonText: "OK",
                        showConfirmButton: true,
                        allowOutsideClick: false,
                        allowEscapeKey: false
                    }).then((confirm) => {
                        if (confirm.isConfirmed) {
                            location.reload();
                        }
                    });
                })
                .catch((error) => {
                    this.validationTaskMemberError = error.response.data;
                });
        },
        // subtask data handling
        addSubtask: function () {
            if(this.addOrUpdateSubtask === false) {
                axios.post('/admin/subtask/create', {
                    board_id: 0,
                    task_id: this.show.id,
                    subtask_name: this.formSubtask.subtask_name,
                    subtask_description: this.formSubtask.subtask_description
                })
                    .then(() => {
                        this.formSubtask.subtask_name = '';
                        this.formSubtask.subtask_description = '';
                        this.validationSubtaskError = '';
                        Swal.fire({
                            title: 'Subtask assigned!',
                            icon: 'success',
                            confirmButtonText: "OK",
                            showConfirmButton: true,
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        }).then((confirm) => {
                            if (confirm.isConfirmed) {
                                this.fetchSubtask();
                            }
                        });
                    })
                    .catch((error) => {
                        this.validationSubtaskError = error.response.data;
                    });
            }
            else {
                axios.put('/admin/subtask/update/' + this.formSubtask.id +'/'+ this.show.id +'/' + this.logged.id, {
                    board_id: this.formSubtask.board_id,
                    subtask_name: this.formSubtask.subtask_name,
                    subtask_description: this.formSubtask.subtask_description,
                    task_id: this.show.id
                })
                    .then(() => {
                        this.fetchSubtask();
                        this.toggleEditSubtask = false;
                        this.addOrUpdateSubtask = false;
                        this.formSubtask.subtask_name = '';
                        this.formSubtask.subtask_description = '';
                    })
                    .catch((error) => {
                        this.validationSubtaskUpdateError = error.response.data;
                    });
            }
        },
        editSubtask: function (item) {
            this.addOrUpdateSubtask = true;
            this.toggleEditSubtask = true;
            this.formSubtask.board_id = item.board_id;
            this.formSubtask.subtask_name = item.subtask_name;
            this.formSubtask.subtask_description = item.subtask_description;
            this.formSubtask.id = item.id;
        },
        deleteSubtask: function (id) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.delete('/admin/subtask/delete/' + id)
                        .then(() => {
                            this.fetchSubtask();
                            this.toggleEditSubtask = false;
                            this.addOrUpdateSubtask = false;
                            this.formSubtask.subtask_name = '';
                            this.formSubtask.subtask_description = '';
                            this.formSubtask.subtask_start_date = '';
                            this.formSubtask.subtask_end_date = '';
                        })
                        .catch((error) => {
                            console.log(error);
                        })
                    Swal.fire(
                        'Deleted!',
                        'A subtask has been deleted.',
                        'success'
                    )
                }
            })

        },
        fetchSubtask: function () {
            axios.get('/admin/subtask/' + this.show.id + '/' + this.logged.id).then((response) => {
                this.subtasks = response.data.data;
            });
        },

        // comment data handling
        addOrUpdateComment: function () {
            if(this.formComment.is_edit === false){
                this.formComment.comment = $('#commentArea').val();
                axios.post('/admin/comment/store',{
                    comment: this.formComment.comment,
                    task_id: this.show.id
                })
                    .then(() => {
                        $('#commentArea').data("emojioneArea").setText('');
                        this.fetchComment();
                        document.getElementById('comment_scroll').scrollIntoView();
                    })
                    .catch((error) => {
                        console.log(error.response.data);
                    });
            }
            else {
                this.formComment.comment = $('#commentArea').data("emojioneArea").getText();
                axios.put('/admin/comment/update/' + this.formComment.id, {
                    comment: this.formComment.comment,
                    task_id: this.show.id
                })
                    .then(() => {
                        this.formComment.is_edit = false;
                        this.formComment.comment = $('#commentArea').data("emojioneArea").setText('');
                        this.fetchComment();
                    })
                    .catch((error) => {
                        console.log(error.response.data);
                    });
            }
        },
        fetchComment: function () {
            axios.get('/admin/comment/' + this.show.id).then((response) => {
                this.comments = response.data;
            });
        },
        deleteComment: function (id) {
            axios.delete('/admin/comment/delete/' + id)
                .then(() => {
                    this.formComment.comment = $('#commentArea').data('emojioneArea').setText('');
                    this.fetchComment();
                })
                .catch((error) => {
                    console.log(error);
                });
        },
        editComment: function (item) {
            this.formComment.is_edit = true;
            this.formComment.comment = $('#commentArea').data("emojioneArea").setText(item.comment);
            this.formComment.id = item.id;
        },

        // project file upload data handling
        fetchFiles: function () {
            axios.get('/admin/file/uploads/' + this.show.id).then((response) => {
                this.attachment = response.data;
            });
        },
        downloadFile: function (file) {
            axios({
                url: '/admin/file/download/' + file,
                method: 'GET',
                responseType: "blob",
            }).then((response) => {
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement("a");

                link.href = url;
                link.setAttribute("download", file);
                link.click();
                link.remove();
            });
        },
        removeFile: function (id, file) {
            axios.delete('/admin/file/destroy/' + id + '/' + file)
                .then((response) => {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "You won't be able to revert this!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire(
                                'File deleted',
                                'File has been deleted',
                                'success'
                            );
                            this.fetchFiles();
                        }
                    })
                })
                .catch((error) => {
                    console.log(error);
                })
        },

        // project data handling
        deleteProject(title) {
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send request to the server
                    axios.delete('/admin/project/delete/' + title)
                        .then(() => {
                            if (result.isConfirmed) {
                                Swal.fire(
                                    'Deleted!',
                                    'Your project has been deleted.',
                                    'success'
                                )
                                window.location.href = "/admin/dashboard";
                            }
                        })
                        .catch(() => {
                            Swal.fire("Failed!", "There was something wrong!", "warning");
                        });
                }
            })
        },
        editProject(item) {
            const vn = this;
            $('#offcanvasEditProjectModal').offcanvas('show');
            vn.formEdit.project_title = item.project_title,
                vn.formEdit.project_description = item.project_description,
                vn.formEdit.project_start_date = item.project_start_date,
                vn.formEdit.project_end_date = item.project_end_date,
                this.selectedId = item.project_id;
        },
        updateProject() {
            axios.put('/admin/project/update/' + this.$project_id, this.formEdit)
                .then(() => {
                    Swal.fire(
                        'Project Updated!',
                        'Your project has been updated.',
                        'success'
                    ).then((confirm) => {
                        if (confirm.isConfirmed) {
                            location.reload();
                        }
                    });

                })
                .catch((error) => {
                    this.validationUpdateProjectError = error.response.data.errors;
                });
        },
        appendMembers: function () {
            this.staff.forEach(person => {
                this.$currentUserArray.push(person.user_id);
            });
        },
        updateMembers() {
            this.formMembers.members = $('#selectMembers').val();
            try {
                axios.put('/admin/update-members/' + this.$project_id, this.formMembers)
                .then(() => {
                    Swal.fire(
                        'Member Invited!',
                        'Member has been invited to the project.',
                        'success'
                    ).then((confirm) => {
                        if (confirm.isConfirmed) {
                            location.reload();
                        }
                    });
                })
                .catch((error) => {
                    this.validationMemberError = error.response.data;
                });
            } catch (e) {
                console.log(e);
            }
        },
        fetchKanbanTask() {
            this.$props.kanban_task.forEach(element => {
                var startDate = new Date(element.task_start_date);
                var endDate = new Date(element.task_due_date);
                var startDateFormatted = (startDate.getMonth() + 1) + "/" + startDate.getDate() + "/" + startDate.getFullYear();
                var endDateFormatted = (endDate.getMonth() + 1) + "/" + endDate.getDate() + "/" + endDate.getFullYear();
                this.$taskColumn.push({'name': element.name, 'y' : [startDateFormatted, endDateFormatted]});
            });
        },
        fetchKanbanBoard() {
            let filter = [];
            this.$props.kanban_board_task.forEach(element => {
                filter = this.$props.kanban_task.filter(e => element.id == e.board_id).map(e => {
                    var startDate = new Date(e.task_start_date);
                    var endDate = new Date(e.task_due_date);
                    var startDateFormatted = (startDate.getMonth() + 1) + "/" + startDate.getDate() + "/" + startDate.getFullYear();
                    var endDateFormatted = (endDate.getMonth() + 1) + "/" + endDate.getDate() + "/" + endDate.getFullYear();
                    return {...e, points: [{'name' : element.name, 'y' : [startDateFormatted, endDateFormatted]}] }
                });

                let mapBoard = [];
                this.$boardColumn.push(element.name);
                filter.filter((value, index, self) => 
                    self.findIndex(v => v.board_name === value.board_name) === index)
                    .forEach(el => {
                    el.points.forEach(ele => {
                        mapBoard = this.$boardColumn
                        .filter(x => x == element.name)
                        .map(elem => { 
                            this.$boardFinal.push({name: el.board_name});
                        });
                    });
                });
                filter.filter((value, index, self) => 
                    self.findIndex(v => v.board_name === value.board_name) === index).forEach(el => {
                        el.points.forEach(ele => {
                            this.$boardFinal.filter(x => x.name == element.name && x.name == ele.name).map(mp => {   
                                this.$boardFinal1.push({...mp, points: []});
                            })
                        });
                })

                filter.forEach(b => {
                    this.$boardFinal1.filter(x => x.name == b.board_name).forEach(item => {
                        var startDate = new Date(b.task_start_date);
                        var endDate = new Date(b.task_due_date);
                        var startDateFormatted = (startDate.getMonth() + 1) + "/" + startDate.getDate() + "/" + startDate.getFullYear();
                        var endDateFormatted = (endDate.getMonth() + 1) + "/" + endDate.getDate() + "/" + endDate.getFullYear();
                        item.points.push({'name' : b.task_name, y: [startDateFormatted, endDateFormatted]})
                    })
                });    
            });
        },


        showModal(tasks) {
            this.clearAssignedUser();
            const vn = this;
            vn.show.id = tasks.id;
            vn.show.name = tasks.name
            vn.show.description = tasks.description
            vn.show.privacy_status = tasks.privacy_status === 0 ? 'Public' : 'Private'
            vn.show.task_start_date = tasks.task_start_date
            vn.show.task_due_date = tasks.task_due_date
            this.assignedUserTask(vn.show.id);
            this.fetchComment();
            this.fetchSubtask();
            this.fetchFiles();
            $('#offcanvasViewTask').offcanvas('show');
        },
        openBoardColorModal() {
            $('#offcanvasBoardChangeColor').offcanvas('show');
        },
        openTaskColorModal() {
            $('#offcanvasTaskChangeColor').offcanvas('show');
        },
        getBoardColor: function() {
            axios.get('/admin/board-color/' + this.$project_id)
                .then((response) => {
                    this.currentBoardColor = response.data.board_color;
                });
        },
        getTaskColor: function() {
            axios.get('/admin/task-color/' + this.$project_id)
                .then((response) => {
                    this.currentTaskColor = response.data.task_color;
                });
        },
        changeBoardColor: function (color) {
            this.currentBoardColor = color
            axios.put('/admin/board-color/update/' + this.$project_id, 
                { 
                    'board_color' : this.currentBoardColor
                }
            )
                .then(() => {
                    Swal.fire(
                        'Board color updated!',
                        'Board color has been changed!',
                        'success'
                    )
                    this.getBoardColor();
                })
                .catch(() => {
                    Swal.fire(
                        'Board color not updated!',
                        'Something went wrong!',
                        'error'
                    )
                });
        },
        changeTaskColor: function (color) {
            this.currentTaskColor = color
            axios.put('/admin/task-color/update/' + this.$project_id,
                { 
                    'task_color' : this.currentTaskColor
                }
            )
                .then(() => {
                    Swal.fire(
                        'Task color updated!',
                        'Task color has been changed!',
                        'success'
                    )
                    this.getTaskColor();
                })
                .catch(() => {
                    Swal.fire(
                        'Task color not updated!',
                        'Something went wrong!',
                        'error'
                    )
                });
        },
        toggleFinishedProject(id) {
            axios.get('/admin/finish-project/' + id).then((response) => {
                Swal.fire(
                    'Project Finished!',
                    'Your project is set to finished.',
                    'success'
                ).then((confirm) => {
                    if (confirm.isConfirmed) {
                        window.location.href = "/admin/dashboard";
                    }
                });
            });
        }
    },
    mounted(){
        const inputElement = document.querySelector('input[id="upload_file"]');
        const pond = FilePond.create(inputElement, {
            labelFileTypeNotAllowed: 'File type not allowed ',
            fileValidateTypeLabelExpectedTypes: 'Allowed file type: .jpg, .jpeg, .png, .xlsx, .xls, .pdf, .docx, .pptx, .txt',
            fileValidateTypeDetectType: (source, type) =>
                new Promise((resolve, reject) => {
                    resolve(type);
                }),
            maxFileSize: '50MB',
            allowMultiple: true,
        });
        pond.setOptions({
            server: {
                process: {
                    url: '/admin/file/upload',
                    method: 'POST',
                    headers: {
                        "X-CSRF-Token" : laravel.csrfToken
                    },
                    withCredentials: false,
                    onload: (response) => {
                        this.fetchFiles();
                    },
                    ondata: (formData) => {
                        formData.append('task_id', this.show.id);
                        return formData;
                    },
                },
            },

            // remove file where has been uploaded one or more
            onprocessfiles: function () {
                Swal.fire(
                    'File Uploaded',
                    'File Uploaded Successfully!',
                    'success'
                )
                pond.removeFiles();
            },
        })

        this.currentColor = VueCookies.get('theme');
        let arrow = document.querySelectorAll(".arrow");
        for (let i = 0; i < arrow.length; i++) {
            arrow[i].addEventListener("click", (e) => {
                let arrowParent = e.target.parentElement.parentElement;//selecting main parent of arrow
                arrowParent.classList.toggle("showMenu");
            });
        }
        let sidebar = document.querySelector(".sidebar");
        let sidebarBtn = document.querySelector(".bx-menu");
        sidebarBtn?.addEventListener("click", () => {
            sidebar.classList.toggle("close");
        });

        var today = new Date()
        var currentDate = (today.getMonth() +1) + "/" + today.getDate() + "/" + today.getFullYear();
        var thresholdDate = norm(currentDate); 

        var startDate = new Date(this.item.project_start_date);
        var endDate = new Date(this.item.project_end_date);
        var startDateFormatted = (startDate.getMonth() + 1) + "/" + startDate.getDate() + "/" + startDate.getFullYear();
        var endDateFormatted = (endDate.getMonth() + 1) + "/" + endDate.getDate() + "/" + endDate.getFullYear();
        var chart = JSC.chart('chartDiv', {
            debug: true,
            title_label_text:'Project: ' + this.item.project_title + ' from ' + startDateFormatted + ' to ' + endDateFormatted,
            type: 'horizontal column solid',
            zAxis_scale_type: 'stacked',
            palette: ['#9adcfa', '#99e4ed', '#d0b6fa'],
            legend: {
                position: 'inside right top',
                template: '%icon %name'
            },
            toolbar_items_export_position:
                'inside bottom left',
            yAxis: {
                scale_type: 'time',
                scale_range_padding: 0.35,
                markers: [
                    {
                        value: currentDate,
                        color: 'red',
                        label_text: 'Now'
                    }
                ]
            },
            defaultPoint_label_autoHide: false,
            defaultPoint: {
                outline_width: 0,
                radius: 0,
                label: {
                    text: pointLabelText,
                    placement: 'outside'
                },
                tooltip:
                    '<b>%name</b> %low - %high<br/>{days(%high-%low)} days'
            },
            yAxis_scale_type: 'time',
            defaultSeries: {
                firstPoint: {
                    outline: { color: 'darkenMore', width: 2 },
                    xAxisTick_label_text: '<b>%value</b>'
                }
            },
            series: this.$boardFinal1
        });

        function pointLabelText(point) { 
            var pY = point.options('y'); 
            var pRange = pY.map(norm); 
            if (thresholdDate > pRange[1]) { 
                return getIconText( 
                'material/navigation/check', 
                '#66BB6A', 
                16 
                ); 
            } else if (thresholdDate > pRange[0]) { 
                return getIconText( 
                'material/notification/sync', 
                '#FDD835', 
                20 
                ); 
            } 
            return getIconText( 
                'material/navigation/close', 
                '#FF5252', 
                16 
            ); 
        }  
        function norm(d) { 
            return new Date(d).getTime(); 
        }     
        function getIconText(name, color, size) { 
            return ( 
                '<icon name=' + 
                name + 
                ' size=' + 
                size + 
                ' fill=' + 
                color + 
                '>'
            ); 
        } 
    }
};
</script>