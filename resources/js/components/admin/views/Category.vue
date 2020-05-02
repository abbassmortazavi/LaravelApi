<template>
  <div>
    <!-- Main content -->
    <section class="content">
      <div class="row">
        <div class="col-sm-12">
          <div class="card card-primary">
            <div class="card-header">
              <h3 class="card-title">Category</h3>
              <portal to="destination" :disabled="false">
                <p>
                  This slot content will be rendered right here as long as the `disabled` prop
                  evaluates to `true`,<br />
                  and will be rendered at the defined destination as when it is set to `false`
                  (which is the default).
                </p>
              </portal>
            </div>
            <!-- /.card-header -->
           
            <!-- form start -->
            <form role="form">
              <div class="card-body">
                <div class="row">
                    <div class="col-6">
                      <div class="form-group">
                        <div class="custom-control custom-checkbox">
                          <input class="custom-control-input" type="checkbox" id="is_free" name="is_free" value="1">
                          <label for="is_free" class="custom-control-label">Free</label>
                        </div>
                      </div>
                    </div>
                    <div class="col-6">
                      <div class="form-group">
                          <div class="custom-control custom-checkbox">
                            <input class="custom-control-input" type="checkbox" id="is_published" name="is_published" value="1">
                            <label for="is_published" class="custom-control-label">Published</label>
                          </div>
                      </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                      <div class="form-group">
                        <label>Category Type</label>
                        <select name="category_type" id="category_type" class="form-control">
                            <option value="0">words</option>
                            <option value="1">listening</option>
                            <option value="2">reading</option>
                            <option value="3">grammer</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-6">
                      <div class="form-group">
                        <label>نوع زیر دسته</label>
                        <select name="list_type" id="list_type" class="form-control">
                          <option value="1">کتاب</option>
                          <option value="2">سر فصل</option>
                          <option value="3">کلمه</option>
                        </select>
                      </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-6">
                      <div class="form-group">
                        <label for="cat_name">Category Name</label>
                        <input type="text" id="category_name" class="form-control" name="category_name" placeholder="Category Name..." value="">
                    </div>
                    </div>
                    <div class="col-6">
                      <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" rows="5" name="description" id="description"></textarea>
                      </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                      <div class="form-group">
                          <label for="parent">Category</label>
                          <select name="parent_category_id" id="parent" @change="getValueOption($event)" v-model="form.parent_category_id" class="form-control">
                              <option value="0" selected>کتاب</option>
                               <option v-for="category in categories" v-bind:value="category.id" :key="category.id">
                                {{ category.category_name }}
                              </option>
                          </select>
                          
                      </div>
                  </div>
                  <div class="col-sm-6">
                      <div id="sub-category-container">
                        <select-component :select_groups="select_groups"></select-component>
                      </div>
                      <input type="hidden" id="parent-category-id" name="parent_category_id" value="0">
                  </div>
                </div>

                <div class="row">
                  <div class="col-6">
                      <div class="form-group">
                          <label for="exampleInputFile">File input</label>
                          <div class="input-group">
                            <div class="custom-file">
                              <input type="file" class="custom-file-input" id="exampleInputFile">
                              <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                            </div>
                            <div class="input-group-append">
                              <span class="input-group-text" id="">Upload</span>
                            </div>
                          </div>
                      </div>
                  </div>
                  <div class="col-6">
                    <div class="form-group">
                        
                    </div>
                  </div>
                </div>
               
              </div>
              <!-- /.card-body -->

              <div class="card-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </section>
    <!-- /.content -->
  </div>
</template>
<script>
export default {
  name:'category',
  data(){
    return{
      form:new Form({
        is_free: '',
        is_pushed: '',
        category_type: '',
        list_type: '',
        category_name: '',
        description: '',
        parent_category_id: '0'
      }),
      errors:{},
      categories: [],
      subCategories: [],
      select_groups: [],
    }
  },
  methods:{
      allCategories(){
        axios.get('/api/v2/allCategories')
        .then(response=>{
          //console.log(response);
            this.categories = response.data.data;
        }).catch(error=>{
          console.log(error);
        });
      },
      getValueOption(event){
         let id = event.target.value;
       
         this.select_groups = [];
     
        axios.get(`/api/v2/subCategory/${id}`)
        .then(response=>{
          //let cats = response.data.data;
          //console.log(response);
          console.log(response.data.data.length);
          if (response.data.data.length > 0)
            {
               this.subCategories = response.data.data;
                let options = [];
               for (let index = 0; index < this.subCategories.length; index++) {
                 const title = this.subCategories[index].category_name;
                 const id = this.subCategories[index].id;
                 let objectOptions = {
                   title: title,
                   value: id
                 }
                 options.push(objectOptions);
                 let groups_count = this.select_groups.length + 1;
                  const new_group = {
                        name: "سلکت " + groups_count,
                        model : `${groups_count}-${index}`,
                        options: options
                  };
                  this.select_groups.push(new_group);
               }
                this.select_groups = this.select_groups[0];
               console.log(this.select_groups);
               
            }
    
        }).catch(error=>{
          console.log(error);
        });
        
      },
      handle_change(event) {
           let id = event.target.value;

           this.getValueOption(id);
            //console.log(id);
      }
      
  },
  mounted(){
    this.$store.dispatch('getCategories');
    //this.categories = this.$store.getters.categories
    this.allCategories();
     console.log(this.select_groups);
  },
  computed:{
     getCategories(){
       return this.$store.getters.categories;
     }
  },
  
}
</script>