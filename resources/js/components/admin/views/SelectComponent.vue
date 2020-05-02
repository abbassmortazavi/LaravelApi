<template>
      <div class="test-page">
            <div class="container">
                  <div class="row">
                        <div class="col-12 mb-3 d-flex align-items-center">
                              <a href="#" @click.prevent="add_group"> add group</a>
                             
                        </div>
                        <!-- <div class="col-12 col-sm-12 col-md-6 col-lg-4 mb-3" v-for="(group , i ) in select_groups" :key="`select-item-${i}`">
                              <p class="mb-2">{{group.name}}</p>
                              <select v-model="group.model" @change="handle_change(group.model)">
                                    <option v-for="(option,j) in group.options" :key="`option-item-${i}-${j}`" :value="option.value">{{option.title}}</option>
                              </select>
                        </div> -->

                        <div class="col-12 col-sm-12 col-md-6 col-lg-4 mb-3">
                              <p class="mb-2">j</p>
                              <select @change="handle_change($event)">
                                    <option v-for="option in select_groups.options" :key="option.value" :value="option.value">{{option.title}}</option>
                              </select>
                        </div>
                  </div>
            </div>
      </div>
</template>

<script>
      export default {
            name: "index",
            props:['select_groups'],
            /* data() {
                  return {
                        select_groups: [
                              {
                                    name: "سلکت 1",
                                    model : '1-2',
                                    options: [{title: "آپشن 1  الف", value: '1-1'}, {title: "آپشن 1  ب", value: '1-2'}]
                              }
                        ],

                  }
            }, */
            methods: {
                  add_group() {
                        /* let groups_count = this.select_groups.length + 1;
                        let new_group = {
                              name: "سلکت " + groups_count,
                              model : `${groups_count}-2`,
                              options: [{title: `آپشن  ${groups_count} الف`, value: `${groups_count}-1`}, {title: `آپشن  ${groups_count} ب`, value: `${groups_count}-2`}]
                        };
                        this.select_groups.push(new_group) */
                  },
                  handle_change(id) {
                        // watch value on developer tools
                        console.log(id)
                  }
            }
      }
</script>
