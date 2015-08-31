<tr>
    <th><label for="w3tc_dbcluster_config">Database cluster:</th>
    <td>
        <input type="submit" id="w3tc_dbcluster_config" name="w3tc_dbcluster_config" class="button"
               value="<?php echo (w3_is_dbcluster() ? 'Edit Database Cluster Configuration' : 'Enable database cluster'); ?>" /><br />
        <span class="description">Create db-cluster-config.php file with your database cluster configuration to enable it.</span>
    </td>
</tr>