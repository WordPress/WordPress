// routes/post-edit/route.ts
var route = {
  async canvas(context) {
    const { params } = context;
    return {
      postType: params.type,
      postId: params.id
    };
  }
};
export {
  route
};
