<template>
    <div :id="id" class="board" @dragover.prevent @drop.prevent="drop">
        <slot/>
    </div>
</template>

<script>
export default {
    props: ["id"],
    methods: {
        init() {
            this.userId = this.user_id;
            this.taskId = this.task_id;
        },

        drop: (e) => {
            const user_id = document
                .querySelector("meta[name='user-id']")
                .getAttribute("content");

            const task_id = document
                .querySelector("input[name='task_id']")
                .getAttribute("value");

            //  get the target task based on subtask_id
            const subtask_id = e.dataTransfer.getData("subtask_id");

            //  get the element based on subtask_id
            const subtask = document.getElementById(subtask_id);

            //  show the target subtask when dropped on the board
            subtask.style.display = "block";

            //  append the target subtask on the board where it is dropped
            e.target.appendChild(subtask);

            let task_data = '';

            fetch('/admin/subtask/' +
                e.target.children[e.target.children.length - 1].id + '/'
                + task_id + '/' + user_id
            )
            .then((response) => response.json())
            .then((response) => {
                task_data = response.data;
                fetch('/admin/subtask/update/' +
                    e.target.children[e.target.children.length - 1].id +
                    '/' + task_id + '/' + user_id, {
                    method: "put",
                    body: JSON.stringify({
                        subtask_id:
                        e.target.children[
                        e.target.children.length - 1
                            ].id,
                        board_id: e.target.id,
                        subtask_name: task_data.subtask_name,
                        subtask_description: task_data.subtask_description,
                        subtask_start_date: task_data.subtask_start_date,
                        subtask_due_date: task_data.subtask_due_date,
                    }),
                    headers: {
                        "Content-Type" : "application/json",
                        "X-CSRF-Token" : laravel.csrfToken
                    }
                })
                
            });
            
        }
    },
};
</script>