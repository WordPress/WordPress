// routes/post/route.ts
import { redirect } from "@wordpress/route";
var route = {
  beforeLoad: ({ params }) => {
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
