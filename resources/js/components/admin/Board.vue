<template>
    <div :id="id" class="board" @dragover.prevent @drop.prevent="drop">
        <slot />
    </div>
</template>

<script>
export default {
    props: ["id"],
    methods: {
        drop: (e) => {
            //  get current user's id from meta tag
            const user_id = document
                .querySelector("meta[name='user-id']")
                .getAttribute("content");

            const project_id = document
                .querySelector("meta[name='project_id']")
                .getAttribute("content");

            //  get the target task based on task_id
            const task_id = e.dataTransfer.getData("task_id");
            //  get the element based on task_id
            const task = document.getElementById(task_id);
            //  show the target task when dropped on the board
            task.style.display = "block";
            //  append the target task on the board where it is dropped
            e.target.appendChild(task);

            let task_data = "";
            let board_data = "";

            //  get task based on task id and current user id
            fetch(
                "/api/task/" +
                    e.target.children[e.target.children.length - 1].id +
                    "/" +
                    user_id +
                    "/" +
                    project_id
            )
                .then((res) => res.json())
                .then((res) => {
                    task_data = res.data;
                    //  get board based on board id and current user id
                    fetch(
                        "/api/board/" +
                            e.target.id +
                            "/" +
                            user_id +
                            "/" +
                            project_id
                    )
                        .then((res) => res.json())
                        .then((res) => {
                            board_data = res.data;
                            //  get task based on id for updating new board's name
                            fetch("/api/task/" + user_id + "/" + project_id, {
                                method: "put",
                                body: JSON.stringify({
                                    task_id:
                                        e.target.children[
                                            e.target.children.length - 1
                                        ].id,
                                    user_id: board_data.user_id,
                                    board_id: board_data.id,
                                    name: task_data.name,
                                    description: task_data.description,
                                    task_start_date: task_data.task_start_date,
                                    task_due_date: task_data.task_due_date,
                                    privacy_status: task_data.privacy_status,
                                    total_subtask: task_data.total_subtask,
                                    total_subtask_done:
                                        task_data.total_subtask_done,
                                }),
                                headers: {
                                    "Content-Type": "application/json",
                                },
                            })
                                // .then(res => res.json())
                                .then(() => window.location.reload())
                                .catch((err) => console.log(err));
                        });
                });
        },
    },
};
</script>
