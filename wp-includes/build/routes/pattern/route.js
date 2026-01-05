// routes/pattern/route.ts
import { redirect } from "@wordpress/route";
var route = {
  beforeLoad: () => {
    throw redirect({
      throw: true,
      to: "/patterns/list/$type",
      params: {
        type: "all"
      }
    });
  }
};
export {
  route
};
