// routes/template-part/route.ts
import { redirect } from "@wordpress/route";
var route = {
  beforeLoad: () => {
    throw redirect({
      throw: true,
      to: "/template-parts/list/$area",
      params: {
        area: "all"
      }
    });
  }
};
export {
  route
};
