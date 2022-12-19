<template>
    <div :id="id" class="task" :draggable="draggable" @dragstart="dragStart" @dragover.stop>
        <slot />
    </div>
</template>

<script>
export default {
    props: ["id", "draggable"],
    methods: {
        dragStart: e => {
            const target = e.target;

            //  set the subtask_id with target task's id
            e.dataTransfer.setData("subtask_id", target.id);

            //  hide the target task when the drag starts
            setTimeout(() => {
                target.style.display = "none";
            }, 0);
        }
    }
};
</script>
