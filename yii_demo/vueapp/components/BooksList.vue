<template>
  <div class="d-flex justify-content-center">
<!--    <select v-model="perPage" class="form-select" aria-label="Default select example">-->
<!--      <option selected>Open this select menu</option>-->
<!--      <option value="5">5</option>-->
<!--      <option value="10">10</option>-->
<!--      <option value="20">20</option>-->
<!--      <option value="30">30</option>-->
<!--    </select>-->
    <paginate
        :page-count="pageCont"
        :page-range="3"
        :margin-pages="2"
        :click-handler="clickCallback"
        :prev-text="'Prev'"
        :prev-class="'cursor-pointer'"
        :next-class="'cursor-pointer'"
        :next-text="'Next'"
        :container-class="'pagination'"
        :page-class="'page-item cursor-pointer'"
    >
    </paginate>
  </div>
  <div class="d-flex flex-wrap mb-3">

    <div class="card m-2" style="width: 19rem;" v-for="book in books" :key="book.message">
      <img
          :src="book.image?book.image:'https://upload.wikimedia.org/wikipedia/commons/thumb/4/41/Noimage.svg/739px-Noimage.svg.png'"
          class="card-img-top" :alt="book.name">
      <div class="card-body">
        <h5 class="card-title">{{ book.name }}</h5>
        <p class="card-text">{{ book.description }}</p>
      </div>
      <div class="card-footer">
        <a :href="'/site/book?id='+book.id" class="btn btn-success">Go</a>
      </div>
    </div>

  </div>
</template>

<script>
import api from '../api';

import Paginate from "vuejs-paginate-next";

export default {
  components: {
    Paginate
  },
  data() {
    return {
      books: [],
      pageCont:0,
      perPage:20,
      columns: [
        {
          title: 'Name',
          dataIndex: 'name',
          key: 'title',
          ellipsis: true
        },
        {
          title: 'Description',
          dataIndex: 'description',
          key: 'description',
        },
        {
          title: 'ISBN',
          dataIndex: 'isbn',
          key: 'isbn',
        },
        {
          title: 'Action',
          key: 'action',
          slots: {customRender: 'action'},
        },
      ]
    };
  },
  methods: {
    showBook(bookId) {
      this.$router.push({name: 'book-item', params: {bookId}});
    },
    async clickCallback(p) {
      console.log('Start ajax');
      let retData = await api.helpPost(
          'books-list',
          {"offset":(p - 1) * this.perPage,"limit":this.perPage}
      );

      this.books = retData.list;
      // this.pageCont = retData.count / this.perPage;
      this.pageCont = Math.ceil(retData.count / this.perPage);
      console.log(p,this.pageCont, retData);
    },

  },
  async mounted() {
    console.log(window.user_access_token);
    let retData = await api.helpPost('books-list');
    console.log('ret', retData);
    this.books = retData.list;
    this.pageCont = retData.count / this.perPage;;
  }
};
</script>