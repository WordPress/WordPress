// routes/post/route.ts
var route = {
  beforeLoad: ({
    params,
    redirect
  }) => {
    throw redirect({
      throw: true,
      to: "/types/$type/list/$slug",
      params: {
        type: params.type,
        slug: "all"
      }
    });
  }
};
export {
  route
};
