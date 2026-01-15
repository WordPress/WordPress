// routes/template/route.ts
import { redirect } from "@wordpress/route";
var route = {
  beforeLoad: () => {
    throw redirect({
      throw: true,
      to: "/templates/list/$activeView",
      params: {
        activeView: "active"
      }
    });
  }
};
export {
  route
};
